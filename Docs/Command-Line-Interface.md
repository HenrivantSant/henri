## 6. Command line interface
The command line presents a clean implementation of the [Symfony Command Line](https://symfony.com/doc/current/components/console.html). See their documentation on how to use the command line general.

### Setup
It's requires a small setup to get to command line running.
1. Create a folder 'bin' in your project root
2. Create a file (without extension) called 'console' with the content from below
```php
#!/usr/bin/env php
<?php
require_once 'vendor/autoload.php';

use Henri\Framework\Console\CLIApplication;

$CLIApplication = new CLIApplication();
$CLIApplication->run();
```
This should be all.

### Default commands
The system comes with a batch of useful commands. Get a list of all available commands by running `php bin/console list` from the command line in the root of your project. The specifics of each command will be explained in their respective chapters.

### Create your own commands
It is very easy to add your own command for running tasks, changing settings, creating cron commands, etc.
```php
namespace Foo\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FooCommand extends Command {

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'foo:bar';

    protected function configure()
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // ...

        return 0;
    }

}
```

&larr; [Making (curl) requests](https://github.com/HenrivantSant/henri/blob/master/Docs/Making-Requests.md#5-making-curl-requests) | [Annotations](https://github.com/HenrivantSant/henri/blob/master/Docs/Annotations.md#7-annotations) &rarr; 