<?php

namespace ImporterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Imported
 *
 * @ORM\Table(name="imported")
 * @ORM\Entity(repositoryClass="ImporterBundle\Repository\ImportedRepository")
 */
class Imported
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="fileName", type="string", length=255, unique=true)
     */
    private $fileName;

    /**
     * @var int
     *
     * @ORM\Column(name="account", type="integer")
     */
    private $account;

    /**
     * @var int
     *
     * @ORM\Column(name="transactions", type="integer")
     */
    private $transactions;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="importedAt", type="datetime")
     */
    private $importedAt;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     *
     * @return Imported
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set account
     *
     * @param integer $account
     *
     * @return Imported
     */
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return int
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set transactions
     *
     * @param integer $transactions
     *
     * @return Imported
     */
    public function setTransactions($transactions)
    {
        $this->transactions = $transactions;

        return $this;
    }

    /**
     * Get transactions
     *
     * @return int
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * Set importedAt
     *
     * @param \DateTime $importedAt
     *
     * @return Imported
     */
    public function setImportedAt($importedAt)
    {
        $this->importedAt = $importedAt;

        return $this;
    }

    /**
     * Get importedAt
     *
     * @return \DateTime
     */
    public function getImportedAt()
    {
        return $this->importedAt;
    }
}

