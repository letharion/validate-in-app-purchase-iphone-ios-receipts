<?php

namespace Letharion\Apple;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Letharion\Apple\itunesReceiptValidator;

class CliValidatorCommand extends Command
{
  protected function configure()
  {
    $this
      ->setName('validate')
      ->setDescription('Validate a iTunes receipt.')
      ->addArgument(
        'receipt',
        InputArgument::REQUIRED,
        'Raw receipt to validate. Prepend with @ to treat it as a filename to read from.'
      )
      ->addArgument(
        'endpoint',
        InputArgument::OPTIONAL,
        'Which endpoint to send the request to.',
        'production'
      )
      ->addArgument(
        'password',
        InputArgument::OPTIONAL,
        'iTunes password for validation. Prepend with @ to treat it as a filename to read from.',
        NULL
      )
      ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $receipts = $input->getArgument('receipt');
    $password = $input->getArgument('password');
    $endpoint = $input->getArgument('endpoint');

    if ($endpoint !== 'production' && $endpoint !== 'sandbox') {
      $output->writeln("Invalid endpoint: $endpoint, choose either production or sandbox");
      return;
    }

    if ($receipts[0] === '@') {
      $receipts = array_filter(explode("\n", file_get_contents(substr($receipts, 1))));
    }
    else {
      $receipts = array($receipts);
    }

    if ($password !== NULL && $password[0] === '@') {
      $password = trim(file_get_contents(substr($password, 1)));
      if ($password === "") {
        $output->writeln("Unable to read password from file.");
        return;
      }
    }

    $endpoint = ($endpoint === 'production') ? itunesReceiptValidator::PRODUCTION_URL : itunesReceiptValidator::SANDBOX_URL;

    $rv = new itunesReceiptValidator($endpoint, $password);
    $results = array_map(function ($receipt) use ($rv) {
      return $rv->validateReceipt($receipt);
    }, $receipts);
    $output->writeln(json_encode($results));
  }
}
