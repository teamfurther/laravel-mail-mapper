<?php

namespace Further\Mailmatch\Normalizers\Validation\Validators;

use Further\Mailmatch\Normalizers\MessageNormalizer;
use Further\Mailmatch\Normalizers\Validation\Contracts\Validator;
use Further\Mailmatch\Normalizers\Validation\Rules\IsArray;
use Further\Mailmatch\Normalizers\Validation\Rules\IsCarbonDatetime;
use Further\Mailmatch\Normalizers\Validation\Rules\IsEmail;
use Further\Mailmatch\Normalizers\Validation\Rules\IsRequired;
use Further\Mailmatch\Normalizers\Validation\Rules\IsString;
use Illuminate\Support\MessageBag;

class NormalizedMessageValidator implements Validator
{
    private $errors;
    private $data;

    public function __construct(array $data)
    {
        $this->errors = new MessageBag();
        $this->data = $data;
    }

    public function errors(): MessageBag
    {
        return $this->errors;
    }

    public function rules(): array
    {
        return [
            'attachments' => [IsArray::class],
            'bcc' => [IsEmail::class],
            'bccName' => [IsString::class],
            'ccRecipients' => [IsArray::class],
            'ccRecipients.email' => [IsEmail::class, IsRequired::class],
            'ccRecipients.name' => [IsString::class],
            'datetime' => [IsCarbonDatetime::class, IsRequired::class],
            'from' => [IsEmail::class, IsRequired::class],
            'fromName' => [IsString::class],
            'html' => [IsString::class, IsRequired::class],
            'plainText' => [IsString::class],
            'subject' => [IsString::class, IsRequired::class],
            'toRecipients' => [IsArray::class, IsRequired::class],
            'toRecipients.email' => [IsEmail::class, IsRequired::class],
            'toRecipients.name' => [IsString::class],
        ];
    }

    public function validate(): bool
    {
        foreach ($this->rules() as $attribute => $rules) {
            foreach ($rules as $rule) {
                $passed = $this->validateRule($rule, $attribute);

                if (!is_null($passed)) {
                    $this->errors->add($attribute, $passed);

                    return false;
                }
            }
        }

        return true;
    }

    private function validateRule($rule, $attributeName): ?string
    {
        if (strpos($attributeName, '.') === false) {
            return $this->validateValue($rule, $attributeName, $this->data[$attributeName]);
        } else {
            list($attribute, $property) = explode('.', $attributeName);

            if (is_array($this->data[$attribute])) {
                foreach ($this->data[$attribute] as $key => $value) {
                    return $this->validateValue(
                        $rule,
                        $attribute . '[' . $key . '].' . $property,
                        $value[$property] ?? null
                    );
                }
            }

            return null;
        }
    }

    private function validateValue($rule, $attribute, $value): ?string
    {
        if ($rule::validate($value)) {
            return null;
        }

        return $rule::message($attribute);
    }
}