<?php

namespace ImporterBundle\Service;

use TransactionsBundle\Repository\TransactionsRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Doctrine\ORM\EntityManager;
use ImporterBundle\Entity\Imported;
use Symfony\Component\Finder\Finder;

use TransactionsBundle\Entity\Transactions;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ImportService
{
    /**
     * @var \TransactionsBundle\Repository\TransactionsRepository
     */
    protected $entityManager;

    public function __construct(
        EntityManager $entityManager,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function importFiles()
    {
        $finder = new Finder();
        $finder->files()->in('../data')->name('*.TAB');

        foreach ($finder as $file) {
            $path = explode("/", $file->getPath());
            $filename = $file->getBasename();

            // Get the date that the file was created
            preg_match("/([A-Z]*)([0-9]{6})/", $filename, $matches);
            $date = str_split($matches[2], 2);
            $createdAt = new \DateTime("$date[1]/$date[2]/$date[0]");

            // Number of lines in files
            $fileContent = file_get_contents($file->getPathName());
            $fileContentArray = explode("\n", $fileContent);
            $transactionsCount = count($fileContentArray);

            // Get File Extension
            $extension = $path[count($path)-2];

            if ($extension == 'TAB') {
                try {
                    $verify = $this
                        ->em
                        ->getRepository('ImporterBundle:Imported')
                        ->getTransactionByFileName($filename);

                    if (count($verify) > 0) {
                        continue;
                    }

                    $account = $path[count($path)-1];

                    $import = new Imported();
                    $import->setFileName($filename);
                    $import->setImportedAt(new \Datetime(date('d-M-Y')));
                    $import->setCreatedAt($createdAt);
                    $import->setAccount($account);
                    $import->setTransactions($transactionsCount);
                    $import->setSuccess(0);

                    $this->em->persist($import);
                    $this->em->flush();

                    $this->importFilesContent($file->getPathName(), $account);

                    $import->setSuccess(1);

                    $this->em->persist($import);
                    $this->em->flush();
                } catch (Excetption $e) {
                    $import->setSuccess(0);
                    echo "ups ups";
                }
            }
        }
    }

    public function importFilesContent($fileLocation, $account)
    {
        $fileContent = file_get_contents($fileLocation);
        $fileContentArray = explode("\n", $fileContent);

        $bankAcc = $this->tokenStorage->getToken()->getUser()->getBankAccount();

        if ($fileContent) {
            foreach ($fileContentArray as $line) {
                // Clean end of string
                $line = rtrim($line);
                if (empty($line)) {
                    continue;
                }

                $info = explode("\t", $line);
                if ($bankAcc != $info[0]) {
                    continue;
                }

                $correctDate = substr($info[2], 0, 4).'-'.substr($info[2], 4, 2).'-'.substr($info[2], 6, 2);
                $Date = new \DateTime($correctDate);

                // Generate Hash
                $hashString = $line;
                $hash = hash('md5', $hashString, false);
                $verify = $this
                    ->em
                    ->getRepository('TransactionsBundle:Transactions')
                    ->getTransactionByHash($hash);

                // Check if this is already on DB and if so continue
                // Should probably clean this a bit
                if ($verify['id'] > 0) {
                    $line = '';
                    continue;
                }

                $transaction = new Transactions();

                $transaction->setTransactionHash($hash);
                $transaction->setCreateAt($Date);
                $transaction->setAmount(floatval(str_replace(',', '.', str_replace('.', '', $info[6]))));
                $transaction->setstartsaldo(floatval(str_replace(',', '.', str_replace('.', '', $info[3]))));
                $transaction->setEndsaldo(floatval(str_replace(',', '.', str_replace('.', '', $info[4]))));

                $transaction->setDescription(utf8_encode($info[7]));
                $transaction->setShortDescription('');
                $transaction->setAccountId($account);

                $this->em->persist($transaction);
                $this->em->flush();
            }
            return;
        }
    }
}
