<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorString validates a string. It also converts the input value to a string.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorString.class.php 12641 2008-11-04 18:22:00Z fabien $
 */
class sfValidatorString extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * max_length: The maximum length of the string
   *  * min_length: The minimum length of the string
   *
   * Available error codes:
   *
   *  * max_length
   *  * min_length
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('max_length', '"%value%" is too long (%max_length% characters max).');
    $this->addMessage('min_length', '"%value%" is too short (%min_length% characters min).');

    $this->addOption('max_length');
    $this->addOption('min_length');
    $this->addOption('validate_encoding', true);

    $this->setOption('empty_value', '');
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    $clean = (string) $value;

    if ($this->getOption('validate_encoding')) {
        if (!mb_check_encoding($clean, mb_strtoupper($this->getCharset()))) {
            throw new sfValidatorError($this, 'invalid', array('value' => $value));
        }
        // Cleaning
        // @see http://webcollab.sourceforge.net/unicode.html
        /*
        if (mb_strtoupper($this->getCharset()) == mb_internal_encoding()) {
            $clean = preg_replace(
                '/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'
                . '|(?<=^|[\x00-\x7F])[\x80-\xBF]+'
                . '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'
                . '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'
                . '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/',
                '�',
                $clean
            );
            $clean = preg_replace(
                '/\xE0[\x80-\x9F][\x80-\xBF]'
                . '|\xED[\xA0-\xBF][\x80-\xBF]/S',
                '?',
                $clean
            );
        }
        */
    }

    $length = function_exists('mb_strlen') ? mb_strlen($clean, $this->getCharset()) : strlen($clean);

    if ($this->hasOption('max_length') && $length > $this->getOption('max_length'))
    {
      throw new sfValidatorError($this, 'max_length', array('value' => $value, 'max_length' => $this->getOption('max_length')));
    }

    if ($this->hasOption('min_length') && $length < $this->getOption('min_length'))
    {
      throw new sfValidatorError($this, 'min_length', array('value' => $value, 'min_length' => $this->getOption('min_length')));
    }

    return $clean;
  }
}
