# Laravel CipherSweet: Searchable Encrypted Attributes

> Hey! I have to say that I'm not very active maintaining this package, but you may always send pull requests!

**Laravel CipherSweet** is a [Laravel](https://laravel.com) implementation of [Paragon Initiative Enterprises CipherSweet](https://ciphersweet.paragonie.com) searchable field level encryption.

![Preview](https://user-images.githubusercontent.com/10741416/68589760-10fae780-048d-11ea-850b-5c7733f0f4f7.png)

Make sure you have some basic understanding of [CipherSweet](https://ciphersweet.paragonie.com) before continuing.

## Installation

Install the package using composer:

```bash
composer require bjornvoesten/laravel-ciphersweet
```

Publish configuration file:

```bash
php artisan vendor:publish --tag=ciphersweet-config
```

Generate an encryption key:

```bash
php artisan ciphersweet:key
```

**Watch out!**
All encrypted columns depend on this key.
If the key changes, the already encrypted columns can not be decrypted anymore!

## Configuration

#### Algorithm

You can change the encryption algorithm by defining the crypto:

```dotenv
ENCRYPTION_CRYPTO=modern/fips
```

For more information about the encryption index algorithms see the [documentation](https://ciphersweet.paragonie.com/internals/blind-index).

## Usage

### Defining encryption

Add the `Bjornvoesten\CipherSweet\Traits\HasEncryption` trait to the model.

```php
    use HasEncryption;
```

Define the attributes that should ben encrypted.

```php
    /**
     * The attributes that can be encrypted.
     *
     * @var array
     */
    protected $encrypted = [
        'social_security_number',
    ];
```

By default the index column name is generated using the name and suffixing it with `_index`.

So the `social_security_number` attribute will use the default index column `social_security_number_index`.

Alternatively you can define multiple indexes per attribute and and define more options. 

```php
    /**
     * Set the social security number attribute encryption.
     *
     * @param \Bjornvoesten\CipherSweet\Contracts\Attribute $attribute
     * @return void
     */
    public function socialSecurityNumberAttributeEncryption($attribute): void
    {
        $attribute
            ->index('social_security_number_full_index', function (Index $index) {
                $index
                    ->bits(32)
                    ->slow();
            })
            ->index('social_security_number_last_four_index', function (Index $index) {
                $index
                    ->bits(16)
                    ->transform(
                        new LastFourDigits()
                    );
            });
    }
```

For more information about the index options see the [documentation](https://ciphersweet.paragonie.com/php/usage).

And make sure you have created the index columns in the database table!

### Searching models

You can search encrypted attributes by using the default `where` clause on the query builder or with the `whereEncrypted` method. 

```php
 \App\User::query()
    ->where('social_security_number', '=', $number)
    ->get();
```

By using the `whereEncrypted` method you can also define the indexes which can be searched.

```php
 \App\User::query()
    ->whereEncrypted('social_security_number', '=', $number, [
        'social_security_number_last_four_index',
    ])
    ->get();
```

**Note** When searching with the `equal to` operator models will be returned when the value is found in one of all available or defined indexes. When searching with the `not equal to` operator all models where the value is not found in any of the available or the defined indexes are returned. 

#### Caveat
 Because of the limited search possibilities in CipherSweet only the `=` and `!=` operators are available when searching encrypted attributes. 

## Testing

```bash
$ composer test
```

To be done.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
