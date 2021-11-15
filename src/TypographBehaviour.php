<?php

namespace Chulakov\Typograph;

use Chulakov\PhpTypograph\TypografInterface;
use yii\base\Behavior;
use yii\base\InvalidArgumentException;
use yii\base\Model;

class TypographBehaviour extends Behavior
{
    /**
     * Атрибуты формы для типографирования
     * @var array
     */
    public $attributes;

    /**
     * @var TypografInterface
     */
    public $typograph;

    public function __construct(TypografInterface $typograph, $config = [])
    {
        $this->typograph = $typograph;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if (!is_array($this->attributes)) {
            throw new InvalidArgumentException('Property attributes must be an array');
        }
        if (!$this->attributes) {
            throw new InvalidArgumentException('Empty values are not allowed');
        }
        parent::init();
    }

    /**
     * Валидация атрибута. Проверка: есть ли у формы указанный атрибут и есть ли к нему доступ
     *
     * @param string $attribute
     */
    protected function validateAttribute($attribute)
    {
        if ($attribute) {
            if (!$this->owner->hasProperty($attribute)) {
                throw new InvalidArgumentException("Form hasn't property: {$attribute}");
            }
            if (!$this->owner->canGetProperty($attribute)) {
                throw new InvalidArgumentException("There isn't access to property: {$attribute}");
            }
        }
    }

    /**
     * @return string[]
     */
    public function events()
    {
        return [
            Model::EVENT_BEFORE_VALIDATE => 'beforeValidate'
        ];
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        $this->typeAttributes();
        return true;
    }

    /**
     * Типографирование атрибутов
     */
    public function typeAttributes()
    {
        foreach ($this->attributes as $keyAttribute => $attribute) {
            if (is_array($attribute)) {
                $this->typeComplexAttr($keyAttribute, $attribute['properties']);
            } else {
                $this->typeSimpleAttr($attribute);
            }
        }
    }

    /**
     * Типографирование обычного атрибута
     *
     * @param string $attribute
     */
    public function typeSimpleAttr($attribute)
    {
        $this->validateAttribute($attribute);
        $text = $this->owner->{$attribute};
        $typeText = $this->typograph->process($text);
        $this->owner->{$attribute} = $typeText;
    }

    /**
     * Типографирование атрибута, представленного массивом
     *
     * @param string $attribute
     * @param array $properties
     */
    public function typeComplexAttr($attribute, array $properties)
    {
        $this->validateAttribute($attribute);
        $attributeValue = $this->owner->{$attribute};
        foreach ($attributeValue as &$value) {
            foreach ($properties as $property) {
                if (isset($value[$property])) {
                    $value[$property] = $this->typograph->process($value[$property]);
                }
            }
        }
        $this->owner->{$attribute} = $attributeValue;
    }
}