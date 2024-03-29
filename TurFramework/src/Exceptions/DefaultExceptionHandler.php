<?php

namespace TurFramework\Exceptions;

class DefaultExceptionHandler
{


    public static function handle($exception)
    {
        $errorData = [];

        $primary_message = $exception->getMessage();
        $secondary_message = self::getSecondaryMessage($exception);

        $multipleMessages = self::getMultipleMessages($exception);

        $className = self::getClassName($exception);

        $trace = $exception->getTrace();


        foreach ($trace as $key => $value) {
            if (isset($value['file'])) {
                $line = $value['line'];
                $file = $value['file'];
                $file_content = file($file);
                $startLine = max(1, $line - 10); // Display 5 lines before the error line
                $endLine = min(count($file_content), $line + 10); // Display 5 lines after the error line

                $errorData[] = [
                    'file' => $file,
                    'line' => $line,
                    'file_content' => $file_content,
                    'start_line' => $startLine,
                    'end_line' => $endLine,
                ];
            }
        }


        return [$errorData, $primary_message, $secondary_message, $multipleMessages, $className, $trace];
    }

    private static function getMultipleMessages($exception)
    {
        return method_exists($exception, 'getMultipleMessages') ? $exception->getMultipleMessages() : '';
    }
    private static function getSecondaryMessage($exception)
    {
        return method_exists($exception, 'getSecondaryMessage') ? $exception->getSecondaryMessage() : '';
    }
    private static function getClassName($exception)
    {
        $className = explode('\\', get_class($exception));

        return end($className);
    }
}
