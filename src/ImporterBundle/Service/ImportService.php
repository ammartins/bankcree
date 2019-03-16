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
        $account
    ) {
        $import = new Imported();

        $import->setFileName($filename);
        $import->setImportedAt(new \Datetime(date('d-M-Y')));
        $import->setCreatedAt($createdAt);
        $import->setAccount($account);

        $this->em->persist($import);
        $this->em->flush();

        return true;
    }

    public function importFiles($importFrom, $userID)
    {
        $finder = new Finder();
        // TAB Files
        $finder->files()->in($importFrom)->name('*.TAB');
        // // CSV FILES
        $finder->files()->in($importFrom)->name('*.csv');

        foreach ($finder as $file) {
            $path = explode("/", $file->getPath());
            $filename = $file->getBasename();
            $account = $path[4];

            // Skip importing if user id does not match folder structure
            if ($account != $userID) {
                continue;
            }

            // Get Extension
            $extension = pathinfo($filename, PATHINFO_EXTENSION);

            $createdAt = new \DateTime();
            if ($extension === 'TAB') {
                // Get the date that the file was created
                preg_match("/([A-Z]*)([0-9]{6})/", $filename, $matches);
                $date = str_split($matches[2], 2);
                $createdAt = new \DateTime("$date[1]/$date[2]/$date[0]");
            }

            $verify = $this
                ->em
                ->getRepository('ImporterBundle:Imported')
                ->getTransactionByFileName($filename);

            if (count($verify) > 0) {
                continue;
            }

            $this->importFilesContent($file->getPathName(), $account);
            $this->saveFile($filename, $createdAt, $account);
        }
    }

    public function importFilesContent($fileLocation, $account)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $bankN = $user->getBankName();

        switch($bankN) {
            case "revolut":
                $this->revolutImport($fileLocation, $account);
                break;
            case "abnamro":
                $this->abnImport($fileLocation, $account);
                break;
            default:
                break;
        }
    }

    public function revolutImport($fileLocation, $account)
    {
        $fileContent = file_get_contents($fileLocation);
        $fileContent = substr( $fileContent, strpos($fileContent, "\n")+1 );

        $fileContentArray = explode("\n", $fileContent);

        $user = $this->tokenStorage->getToken()->getUser();

        if ($fileContent) {
            $fileContentArray = array_reverse($fileContentArray);
            foreach ($fileContentArray as $line) {
                // Clean end of string
                $line = rtrim($line);
                if (empty($line)) {
                    continue;
                }

                $hash = hash('md5', $line, false);
                $verify = $this
                    ->em
                    ->getRepository('TransactionsBundle:Transactions')
                    ->getTransactionByHash($hash);

                if ($verify['id'] > 0) {
                    $line = '';
                    continue;
                }

                $info = explode(";", $line);

                $date = explode(" ", $info[0]);
                $correctDate = $date[0].' '.$date[1].' '.date('Y');
                if (is_numeric($date[0])) {
                    $correctDate = $date[1].' '.$date[2].' '.$date[0];
                }

                // Income or Out
                $amount = (floatval(str_replace(',', '.', str_replace('.', '', $info[2]))))*-1;
                $endSaldo = floatval(str_replace(',', '.', str_replace('.', '', $info[6])));
                $startSaldo = $endSaldo - $amount;
                if ($info[3] > 0) {
                    $amount = floatval(str_replace(',', '.', str_replace('.', '', $info[3])));
                    $startSaldo = $endSaldo - $amount;
                }

                $date = new \DateTime($correctDate);

                $transaction = new Transactions();

                $transaction->setTransactionHash($hash);
                $transaction->setCreateAt($date);
                $transaction->setAmount($amount);
                $transaction->setStartSaldo($startSaldo);
                $transaction->setEndsaldo($endSaldo);
                $transaction->setDescription(utf8_encode($info[1]));
                $transaction->setShortDescription($info[4]);
                $transaction->setAccountId($account);

                $this->em->persist($transaction);
                $this->em->flush();
                // dump($info);
                // dump($transaction);
            }
        }
        // exit;
    }

    public function abnImport($fileLocation, $account)
    {
        $fileContent = file_get_contents($fileLocation);
        $fileContentArray = explode("\n", $fileContent);

        $user = $this->tokenStorage->getToken()->getUser();

        $bankAcc = $user->getBankAccount();

        if ($fileContent) {
            $fileContentArray = array_reverse($fileContentArray);
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
                $date = new \DateTime($correctDate);

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
                $transaction->setCreateAt($date);
                $transaction->setAmount(floatval(str_replace(',', '.', str_replace('.', '', $info[6]))));
                $transaction->setstartsaldo(floatval(str_replace(',', '.', str_replace('.', '', $info[3]))));
                $transaction->setEndsaldo(floatval(str_replace(',', '.', str_replace('.', '', $info[4]))));

                $transaction->setDescription(utf8_encode($info[7]));
                $transaction->setShortDescription('');
                $transaction->setAccountId($account);

                $this->em->persist($transaction);
                $this->em->flush();
            }
        }
    }
}
