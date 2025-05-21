<?php
namespace App\Traits;

use Illuminate\Support\Facades\Log as FacadesLog;

/**
 * Traits are a mechanism for code reuse in single inheritance languages such as PHP. A Trait is intended to reduce some
 * limitations of single inheritance by enabling a developer to reuse sets of methods freely in several independent 
 * classes living in different class hierarchies. The semantics of the combination of Traits and classes is defined in 
 * a way which reduces complexity, and avoids the typical problems associated with multiple inheritance and Mixins.
 * 
 * A Trait is similar to a class, but only intended to group functionality in a fine-grained and consistent way. 
 * It is not possible to instantiate a Trait on its own. It is an addition to traditional inheritance and enables 
 * horizontal composition of behavior; that is, the application of class members without requiring inheritance.
 * 
 * Trait for working with error log.
 */
trait Log
{
    /**
     * Save the error in the laravel logs. If the APP_ENV is not production or testing, usually development, then the 
     * error will be displayed to the user.
     *
     * @param string $pType - The type (error, warning, info, success)
     * @param \Exception $pException - The exception thrown
     */
    public static function save(string $pType, \Exception $pException)
    {
        $message = "{$pType}: {$pException->getMessage()}. File: {$pException->getFile()}. Line: {$pException->getLine()}";
        if (
            (
                getenv('APP_ENV') != 'production' && getenv('APP_ENV') != 'testing'
            ) && $pType === 'error'
        ) {
            dd($message);
        }

        FacadesLog::$pType($message);
    }
}