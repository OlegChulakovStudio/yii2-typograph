<?php

namespace Chulakov\Typograph;

use Yii;
use yii\base\Component;
use Chulakov\PhpTypograph\TypografInterface;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\di\Instance;

class TypographComponent extends Component implements TypografInterface
{
    /**
     * @var string
     * Путь файла с новыми (добавочными) правилами типографа
     */
    public $additionalRulesPath;

    /**
     * @var string
     * Путь файла с изменениями старых правил типографа
     */
    public $changedRulesPath;

    /**
     * @var string
     * Класс типографа
     */
    public $typographClass = 'Chulakov\PhpTypograph\TypographFacade';

    /**
     * @var TypografInterface
     * Объект типографа внутри компонента
     */
    protected $typograph;

    /**
     * Инициализация компонента. Состоит из двух действий:
     * 1. загрузка конфигураций с правилами типографа
     * 2. инициализация типографа
     *
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (is_subclass_of($this->typographClass, 'TypografInterface')) {
            throw new InvalidArgumentException('Typograph should be a realization of TypografInterface');
        }

        $additionalRules = $this->loadConfigRules($this->additionalRulesPath);
        $changedRules = $this->loadConfigRules($this->changedRulesPath);

        $this->typograph = Instance::ensure([
            'additionalRules' => $additionalRules,
            'changedRules' => $changedRules
        ], $this->typographClass);
        parent::init();
    }

    /**
     * Типографирование текста
     *
     * @param string $text
     * @return string
     */
    public function process($text)
    {
        return $this->typograph->process($text);
    }

    /**
     * Загрузка файла конфигурации с новыми правилами или изменениями старых правил
     *
     * @param string $configPath
     * @return array
     */
    private function loadConfigRules($configPath)
    {
        $configPath = Yii::getAlias($configPath);
        if (is_file($configPath)) {
            $data = include $configPath;
            if (!empty($data) && is_array($data)) {
                return $data;
            }
        }
        return [];
    }
}