# ai-commit

![](docs/ai-commit.gif)

[简体中文](README-zh_CN.md) | [ENGLISH](README.md)

> Automagically generate conventional commit messages with AI. - 使用 AI 自动生约定式提交信息。

[![tests](https://github.com/guanguans/ai-commit/workflows/tests/badge.svg)](https://github.com/guanguans/ai-commit/actions)
[![check & fix styling](https://github.com/guanguans/ai-commit/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/guanguans/ai-commit/actions)
[![codecov](https://codecov.io/gh/guanguans/ai-commit/branch/main/graph/badge.svg?token=URGFAWS6S4)](https://codecov.io/gh/guanguans/ai-commit)
[![Latest Stable Version](https://poser.pugx.org/guanguans/ai-commit/v)](//packagist.org/packages/guanguans/ai-commit)
[![Total Downloads](https://poser.pugx.org/guanguans/ai-commit/downloads)](//packagist.org/packages/guanguans/ai-commit)
[![License](https://poser.pugx.org/guanguans/ai-commit/license)](//packagist.org/packages/guanguans/ai-commit)
![GitHub repo size](https://img.shields.io/github/repo-size/guanguans/ai-commit)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/guanguans/ai-commit)

## Requirement

* PHP >= 7.2

## Installation

### Download the [ai-commit](./builds/ai-commit) file

```bash
curl 'https://raw.githubusercontent.com/guanguans/ai-commit/main/builds/ai-commit' -o ai-commit --progress
chmod +x ai-commit
```

### Install via Composer

```bash
# Global
composer global require guanguans/ai-commit --dev -v

# Local
composer require guanguans/ai-commit --dev -v
```

## Usage

### First use requires configuration [OpenAI API key](https://platform.openai.com/account/api-keys)

```bash
./ai-commit config set generators.openai.api_key sk-... --global
```

### Generate and commit message

```bash
./ai-commit commit
```

![](docs/ai-commit.gif)

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

* [guanguans](https://github.com/guanguans)
* [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
