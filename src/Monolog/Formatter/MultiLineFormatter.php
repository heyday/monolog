<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Formatter;

/**
 * Formats incoming records into a multi-line string
 *
 * This is especially useful for logging to files
 *
 * @author Cam Spiers <cameron@heyday.co.nz>
 */
class MultiLineFormatter implements FormatterInterface
{
    const SIMPLE_DATE = "Y-m-d H:i:s";
    const SIMPLE_FORMAT = <<<FORMAT
[%datetime%]
Channel: %channel%
Level: %level_name%
Message: %message%
Context: %context%
Extra: %extra%


FORMAT;

    protected $format;

    /**
     * @param string $format     The format of the message
     */
    public function __construct($format = null, $dateFormat = null)
    {
        $this->dateFormat = $dateFormat ?: static::SIMPLE_DATE;
        $this->format = $format ?: static::SIMPLE_FORMAT;
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {

        $output = $this->format;
        foreach ($record['extra'] as $var => $val) {
            if (false !== strpos($output, '%extra.'.$var.'%')) {
                $output = str_replace('%extra.'.$var.'%', $this->convertToString($val), $output);
                unset($record['extra'][$var]);
            }
        }
        foreach ($record as $var => $val) {
            $output = str_replace('%'.$var.'%', $this->convertToString($val), $output);
        }

        return $output;
    }

    public function formatBatch(array $records)
    {
        $message = '';
        foreach ($records as $record) {
            $message .= $this->format($record);
        }

        return $message;
    }

    protected function convertToString($data)
    {
        if (null === $data || is_scalar($data)) {
            return (string) $data;
        }

        if ($data instanceof \DateTime) {
            return $data->format($this->dateFormat);
        }

        ob_start();

        var_dump($data);

        return ob_get_clean();
    }
}
