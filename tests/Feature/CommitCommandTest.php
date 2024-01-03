<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/ai-commit.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use App\Commands\CommitCommand;
use App\Exceptions\TaskException;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

it('can from shell commandline create process', function (): void {
    $createProcess = function () {
        return $this->createProcess('git status');
    };
    expect($createProcess->call(app(CommitCommand::class)))->toBeInstanceOf(Process::class);
})->group(__DIR__, __FILE__)->skip();

it('will throw TaskException(not a git repository)', function (): void {
    $this->artisan(CommitCommand::class, [
        'path' => $this->app->basePath('../'),
        '--config' => config_path('ai-commit.php'),
    ]);
})
    ->group(__DIR__, __FILE__)
    ->throws(ProcessFailedException::class, 'fatal: ');

it('will throw TaskException(no cached files to commit)', function (): void {
    // 重置暂存区
    Process::fromShellCommandline('git reset', repository_path())->mustRun();

    $this->artisan(CommitCommand::class, [
        'path' => repository_path(),
        '--generator' => 'openai',
    ]);
})
    ->depends('it will throw TaskException(not a git repository)')
    ->group(__DIR__, __FILE__)
    ->throws(TaskException::class, 'There are no cached files to commit. Try running `git add` to cache some files.');

it('will throw TaskException(The generated commit message is an invalid JSON)', function (): void {
    // 添加文件到暂存区
    file_put_contents(repository_path('playground.random'), Str::random());
    Process::fromShellCommandline('git rm -rf --cached repository/', fixtures_path())->mustRun();
    Process::fromShellCommandline('git add playground.random', repository_path())->mustRun();

    Http::fake(function (): PromiseInterface {
        return Http::response([
            'id' => 'cmpl-6n1qMNWwuF5SYBcS4Nev5sr4ACpEB',
            'object' => 'text_completion',
            'created' => 1677143178,
            'model' => 'text-davinci-003',
            'choices' => [
                0 => [
                    'text' => 'invalid json', // 无效响应
                    'index' => 0,
                    'logprobs' => null,
                    'finish_reason' => 'stop',
                ],
            ],
            'usage' => [
                'prompt_tokens' => 749,
                'completion_tokens' => 159,
                'total_tokens' => 908,
            ],
        ]);
    });

    $this->artisan(CommitCommand::class, [
        'path' => repository_path(),
        '--generator' => 'openai',
    ]);
})
    ->depends('it will throw TaskException(no cached files to commit)')
    ->group(__DIR__, __FILE__)
    ->throws(TaskException::class, 'The generated commit message(');

it('can generate and commit message', function (): void {
    // 设置 git 信息
    Process::fromShellCommandline('git config user.email yaozm', repository_path())->mustRun();
    Process::fromShellCommandline('git config user.name ityaozm@gmail.com', repository_path())->mustRun();
    setup_http_fake();

    $message = collect([
        'subject' => 'Fix(OpenAIGenerator): Debugging output',
        'body' => '- Add var_dump() for debugging output- Add var_dump() for stream response',
    ]);

    $this
        ->artisan(CommitCommand::class, [
            'path' => repository_path(),
            '--generator' => 'openai',
            '--no-edit' => true,
            '--no-verify' => true,
            '--verbose' => true,
        ])
        ->expectsTable(
            $message->keys()->all(),
            [$message->all()]
        )
        // ->expectsChoice('Please choice a commit message', $message->pluck('subject', 'id')->first(), $message->pluck('subject', 'id')->all())
        // ->expectsQuestion('Please choice a commit message', '<comment>regenerating...</comment>')
        ->expectsConfirmation('Do you want to commit this message?', 'yes')
        ->assertSuccessful();
})
    ->depends('it will throw TaskException(The generated commit message is an invalid JSON)')
    ->group(__DIR__, __FILE__);

afterAll(static function (): void {
    // 清理 playground 仓库
    Process::fromShellCommandline('git reset HEAD^', repository_path())->run();
    Process::fromShellCommandline('git checkout -- .', repository_path())->run();
    Process::fromShellCommandline('git add tests/Fixtures/repository/', base_path())->mustRun();
});
