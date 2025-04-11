<?php

namespace ProjectSaturnStudios\Vibes\Concerns\Transports;

use Illuminate\Support\Facades\Log;

/**
 * Provides utility methods for manipulating PHP's output buffer.
 *
 * This trait is typically used in SSE transport classes to manage output buffering
 * and script execution time limits, ensuring proper streaming behavior.
 *
 * @package ProjectSaturnStudios\Vibes\Concerns\Transports
 * @since 0.4.0
 */
trait OutputBufferManipulation
{
    /**
     * Cleans any existing output buffers.
     *
     * Ensures that no previous output interferes with the SSE stream.
     *
     * @return void
     */
    protected function clean_output_buffer() : void
    {
        if (ob_get_level()) {
            ob_end_clean();
        }
    }

    /**
     * Sets the script execution time limit based on configuration.
     *
     * Allows overriding the default PHP execution time limit for long-running SSE connections.
     *
     * @return void
     */
    protected function override_execution_time() : void
    {
        // Set time limit if specified
        if (config('vibes.service_info.listener_execution_time') > 0) {
            Log::info("Setting time limit to " . config('vibes.service_info.listener_execution_time'));
            set_time_limit(config('vibes.service_info.listener_execution_time'));
        }
    }

    /**
     * Flushes the output buffer to send data immediately.
     *
     * Ensures that messages are sent to the client without delay.
     *
     * @return void
     */
    protected function flushings() : void
    {
        // Flush output
        if (ob_get_level() > 0) {ob_flush();}
        flush();
    }
}
