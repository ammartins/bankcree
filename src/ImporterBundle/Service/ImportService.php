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
    ) {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function saveFile(
        $filename,
        $createdAt,
        $account,
        $importedResult
    ) {
        $import = new Imported();
        $import->setFileName($filename);
        $import->setImportedAt(new \Datetime(date('d-M-Y')));
        $import->setCreatedAt($createdAt);
        $import->setAccount($account);
        $import->setTransactions($importedResult['total']);
        $import->setSuccess($importedResult['success']);
        $import->setFailed($importedResult['failed']);

        $this->em->persist($import);
        $this->em->flush();

        return true;
    }

    public function importFiles($importFrom, $fileName = "")
    {
        $finder = new Finder();
        $finder->files()->in($importFrom)->name('*.TAB');

        foreach ($finder as $file) {
            $path = explode("/", $file->getPath());
            $filename = $file->getBasename();

            // Get the date that the file was created
            preg_match("/([A-Z]*)([0-9]{6})/", $filename, $matches);
            $date = str_split($matches[2], 2);
            $createdAt = new \DateTime("$date[1]/$date[2]/$date[0]");

            // Get File Extension
            $extension = $path[count($path)-2];

            if ($extension == 'TAB') {
                $verify = $this
                    ->em
                    ->getRepository('ImporterBundle:Imported')
                    ->getTransactionByFileName($filename);

                if (count($verify) > 0) {
                    continue;
                }

                $account = $path[count($path)-1];

                $importedResult = $this->importFilesContent($file->getPathName(), $account);
                $this->saveFile($filename, $createdAt, $account, $importedResult);
            }
        }
    }

    public function importFilesContent($fileLocation, $account)
    {
        $fileContent = file_get_contents($fileLocation);
        $fileContentArray = explode("\n", $fileContent);

        $bankAcc = $this->tokenStorage->getToken()->getUser()->getBankAccount();
        $result = array(
            'failed' => 0,
            'success' => 0,
            'total' => 0,
        );

        if ($fileContent) {
            foreach ($fileContentArray as $line) {
                // Clean end of string
                $line = rtrim($line);
                if (empty($line)) {
                    continue;
                }
                $result['total'] += 1;

                $info = explode("\t", $line);
                if ($bankAcc != $info[0]) {
                    $result['failed'] += 1;
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
                $result['success'] += 1;
            }
            return $result;
        }
    }
}
