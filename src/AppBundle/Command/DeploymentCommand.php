<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DeploymentCommand extends Command
{
    private $instanceName;
    private $remoteAppRoot;

    /**
     * @var OutputInterface $output
     */
    private $output;
    private $hostIp;
    private $credentialsLocation;
    private $deployUser;
    private $sshUser;

    /**
     * DeploymentCommand constructor.
     *
     * @param string $hostIp
     * @param string $sshUser
     * @param string $credentialsLocation
     */
    public function __construct($hostIp, $sshUser, $credentialsLocation)
    {
        $this->credentialsLocation = $credentialsLocation;
        $this->deployUser = $sshUser;
        $this->sshUser = $sshUser;
        $this->hostIp = $hostIp;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('swapp:deploy');
        $this->setDescription('Deploys swapp project.');
        $this->addOption(
            'instance-name',
            null,
            InputOption::VALUE_REQUIRED,
            'instance-name; currently supported are beta1 till beta11 and swapp'
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $this->instanceName = $input->getOption('instance-name');

        $this->remoteAppRoot = sprintf('/var/www/%s', $this->instanceName);

        $output->writeln(
            sprintf(
                '<info>Deploy swapp to [ %s ] using [ %s ]</info>',
                $this->remoteAppRoot,
                $this->instanceName
            )
        );
        $this->buildProject();

        $this->doDeploy();

        $this->info('SUCCESS');
    }

    private function doDeploy()
    {
        $this->rSync();

        $this->clearRemoteCache();
        $this->doctrineRemoteRecreation();
        $this->remoteWarmupCache();
        $this->changeOwnerToWwwData();

        $this->clearBuildDir();
    }

    private function changeOwnerToWwwData()
    {
        $this->info('changeOwnerToWwwData');
        $this->executeRemoteCommand(
            sprintf(
            'chown -R www-data:www-data %s/var/logs/ %s/var/cache/',
                $this->remoteAppRoot,
                $this->remoteAppRoot
            )
        );
    }

    private function doctrineRemoteRecreation()
    {
        $this->info('doctrineRemoteRecreation');
        $this->executeRemoteCommand(
            sprintf(
                'php %s/bin/console doctrine:schema:drop --full-database --force --env=prod',
                $this->remoteAppRoot
            )
        );
        $this->executeRemoteCommand(
            sprintf(
                'php %s/bin/console doctrine:schema:create --no-interaction --env=prod',
                $this->remoteAppRoot
            )
        );
        $this->executeRemoteCommand(
            sprintf(
                'php %s/bin/console hautelook_alice:doctrine:fixtures:load --no-interaction --env=test',
                $this->remoteAppRoot
            )
        );
    }

    private function clearRemoteCache()
    {
        $this->info('clearRemoteCache');

        $this->executeRemoteCommand(
            sprintf(
                'rm -rf %s/var/cache/*',
                $this->remoteAppRoot
            )
        );
    }

    private function remoteWarmupCache()
    {
        $this->info('warmupRemoteCache');
        $this->executeRemoteCommand(
            sprintf(
                'php %s/bin/console cache:clear --no-debug --no-interaction --env=prod',
                $this->remoteAppRoot
            )
        );
    }

    private function rSync()
    {
        $this->info('rSync');

        $syncCommand = sprintf(
            'rsync %s %s build/* %s@%s:%s',
            '--recursive --cvs-exclude --verbose --copy-links --delete --delete-after --delete-excluded --links --times',
            '--exclude=/var/cache/* --exclude=/var/logs/* --exclude=/web/app_*.php --exclude=/web/config.php',
            $this->deployUser,
            $this->hostIp,
            $this->remoteAppRoot
        );

        $this->runProcess($syncCommand);
    }

    private function buildProject()
    {
        $this->info('Build Project');
        $this->clearBuildDir();
        $this->checkoutBranch();
        $this->installInBuildDir();
        $this->setCredentials();
    }

    private function setCredentials()
    {
        $credentialsTmpDir = 'build/credentials_tmp';
        $this->runProcess(sprintf('git clone %s %s', $this->credentialsLocation, $credentialsTmpDir));

        $credentialsFile = sprintf('%s/%s/%s/parameters.yml', $credentialsTmpDir, 'swapp', $this->instanceName);
        $credentialsDestination = 'build/app/config';

        $this->runProcess(sprintf('mv %s %s', $credentialsFile, $credentialsDestination));
        $this->runProcess(sprintf('rm -rf %s', $credentialsTmpDir));
    }

    private function installInBuildDir()
    {
        $this->info('installInBuildDir');

        $this->runProcess('cd build && curl -sS https://getcomposer.org/installer | php');
        $this->runProcess('cd build && php composer.phar install --optimize-autoloader');
        $this->runProcess('cd build && php composer.phar dump-autoload --optimize');
    }

    private function checkoutBranch()
    {
        $this->info('checkoutBranch');
        $findBranchNameCmd = 'git rev-parse --abbrev-ref HEAD';
        $branchNameProcess = $this->runProcess($findBranchNameCmd);
        $branchName = trim($branchNameProcess->getOutput());

        $cloneCmd = sprintf('git clone --branch %s %s %s', $branchName, getcwd(), 'build/');
        $this->runProcess($cloneCmd);
    }

    private function clearBuildDir()
    {
        $this->info('clearBuildDir');
        $this->runProcess('rm -rf build');
    }

    /**
     * @param string $cmd
     *
     * @return Process
     */
    private function runProcess($cmd)
    {
        $this->output->writeln($cmd);
        try {
            $process = new Process($cmd);
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            throw new \RuntimeException($e->getMessage());
        }

        return $process;
    }

    /**
     * @param string $cmd
     */
    private function executeRemoteCommand($cmd)
    {
        $this->runProcess(
            sprintf(
                'ssh -Tp %s %s@%s %s',
                escapeshellarg(22),
                escapeshellarg($this->sshUser),
                escapeshellarg($this->hostIp),
                escapeshellarg(
                    sprintf(
                        'sudo -u %s %s',
                        escapeshellarg('root'),
                        $cmd
                    )
                )
            )
        );
    }

    /**
     * @param string $message
     */
    private function info($message)
    {
        $this->output->writeln(sprintf('<info>%s</info>', $message));
    }
}
