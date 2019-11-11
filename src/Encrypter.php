<?php

namespace Bjornvoesten\CipherSweet;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use ParagonIE\CipherSweet\Backend\FIPSCrypto;
use ParagonIE\CipherSweet\Backend\ModernCrypto;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\CipherSweet;
use ParagonIE\CipherSweet\EncryptedField;
use ParagonIE\CipherSweet\KeyProvider\StringProvider;

class Encrypter implements Contracts\Encrypter
{
    /**
     * @var \ParagonIE\CipherSweet\KeyProvider\StringProvider
     */
    protected $provider;

    /**
     * @var \ParagonIE\CipherSweet\Backend\FIPSCrypto
     */
    protected $crypto;

    /**
     * @var \ParagonIE\CipherSweet\CipherSweet
     */
    protected $engine;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->provider = $this->createProviderInstance();
        $this->crypto = $this->createCryptoInstance();

        $this->engine = new CipherSweet(
            $this->provider, $this->crypto
        );
    }

    /**
     * @return \ParagonIE\CipherSweet\Contract\KeyProviderInterface
     * @throws \Exception
     */
    protected function createProviderInstance()
    {
        return new StringProvider(
            config('ciphersweet.key')
        );
    }

    /**
     * @return \ParagonIE\CipherSweet\Contract\BackendInterface
     */
    protected function createCryptoInstance()
    {
        switch (config('ciphersweet.crypto')) {
            case 'fips':
                return new FIPSCrypto();
            default:
                return new ModernCrypto();
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $column
     * @return string
     * @throws \Exception
     * @throws \Throwable
     */
    public function encrypt(Model $model, string $column)
    {
        $field = $this->createFieldWithIndexes(
            $model, $column
        );

        list ($text, $indexes) = $field->prepareForStorage(
            (string)$model->{$column}
        );

        $indexes[$column] = $text;

        $model->forceFill($indexes);

        return $model;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $column
     * @return string
     * @throws \Exception
     */
    public function decrypt(Model $model, string $column)
    {
        $field = $this->createFieldWithIndexes(
            $model, $column
        );

        $value = $field->decryptValue(
            $model->{$column}
        );

        $model->forceFill([
            $column => $value,
        ]);

        return $model;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $column
     * @param $value
     * @return array
     * @throws \Exception
     */
    public function columnIndexes(Model $model, string $column, $value)
    {
        $field = $this->createFieldWithIndexes(
            $model, $column
        );

        $indexes = $field->getAllBlindIndexes($value);

        if (empty($indexes)) {
            return [];
        }

        return $indexes;
    }

    /**
     * @param string $column
     * @return string
     */
    protected function guessAttributeEncryptionMethod(string $column): string
    {
        return lcfirst(str_replace('_', '', Str::title($column))) . 'AttributeEncryption';
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param $column
     * @return \ParagonIE\CipherSweet\EncryptedField
     * @throws \Exception
     */
    protected function createFieldWithIndexes(Model $model, $column)
    {
        $method = $this->guessAttributeEncryptionMethod($column);

        $table = $model->getTable();

        $field = new EncryptedField(
            $this->engine, $table, $column
        );

        if (method_exists($model, $method)) {
            $col = new Attribute();

            $model->{$method}($col);

            $indexes = $col->toArray();

            /** @var \Bjornvoesten\CipherSweet\Index $index */
            foreach ($indexes as $index) {
                $field->addBlindIndex(
                    new BlindIndex(
                        $index->name,
                        $index->transformers,
                        $index->bits,
                        $index->fast
                    )
                );
            }
        } else {
            $field->addBlindIndex(
                new BlindIndex("{$column}_index", [], 256, true)
            );
        }

        return $field;
    }
}
