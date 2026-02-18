<?php

namespace App\Providers;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\WorkerStarting;
use Illuminate\Queue\Events\WorkerStopping;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class QueueLoggingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(CommandStarting::class, function (CommandStarting $event): void {
            if ($event->command !== 'queue:work') {
                return;
            }

            Log::info('queue:work command starting.', [
                'arguments' => $event->input->getArguments(),
                'options' => $event->input->getOptions(),
            ]);
        });

        Event::listen(CommandFinished::class, function (CommandFinished $event): void {
            if ($event->command !== 'queue:work') {
                return;
            }

            Log::info('queue:work command finished.', [
                'exit_code' => $event->exitCode,
                'options' => $event->input->getOptions(),
            ]);
        });

        Event::listen(WorkerStarting::class, function (WorkerStarting $event): void {
            Log::info('Queue worker starting.', [
                'connection' => $event->connectionName,
                'queue' => $event->queue,
                'options' => [
                    'delay' => $event->workerOptions->delay ?? null,
                    'max_tries' => $event->workerOptions->maxTries ?? null,
                    'sleep' => $event->workerOptions->sleep ?? null,
                ],
                'pid' => getmypid(),
            ]);
        });

        Event::listen(WorkerStopping::class, function (WorkerStopping $event): void {
            Log::warning('Queue worker stopping.', [
                'status' => $event->status,
                'pid' => getmypid(),
            ]);
        });

        Queue::before(function (JobProcessing $event): void {
            Log::info('Queue job processing.', [
                'connection' => $event->connectionName,
                'queue' => $event->job->getQueue(),
                'job' => $event->job->resolveName(),
                'uuid' => method_exists($event->job, 'uuid') ? $event->job->uuid() : null,
            ]);
        });

        Queue::after(function (JobProcessed $event): void {
            Log::info('Queue job processed.', [
                'connection' => $event->connectionName,
                'queue' => $event->job->getQueue(),
                'job' => $event->job->resolveName(),
                'uuid' => method_exists($event->job, 'uuid') ? $event->job->uuid() : null,
            ]);
        });

        Queue::failing(function (JobFailed $event): void {
            Log::error('Queue job failed.', [
                'connection' => $event->connectionName,
                'queue' => $event->job->getQueue(),
                'job' => $event->job->resolveName(),
                'uuid' => method_exists($event->job, 'uuid') ? $event->job->uuid() : null,
                'exception' => $event->exception->getMessage(),
            ]);
        });

        Queue::exceptionOccurred(function (JobExceptionOccurred $event): void {
            Log::error('Queue job exception occurred.', [
                'connection' => $event->connectionName,
                'queue' => $event->job->getQueue(),
                'job' => $event->job->resolveName(),
                'uuid' => method_exists($event->job, 'uuid') ? $event->job->uuid() : null,
                'exception' => $event->exception->getMessage(),
            ]);
        });
    }
}
