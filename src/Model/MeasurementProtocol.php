<?php

namespace Iidev\GoogleTagManager\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Measurement Protocol
 *
 * @ORM\Entity
 * @ORM\Table(name="measurement_protocol")
 */
class MeasurementProtocol extends \XLite\Model\AEntity
{
    /**
     * Unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $id;

    /**
     * Order ID
     *
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $orderId;

    /**
     * MP Client ID
     *
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $mpClientId;

    /**
     * MP Session ID
     *
     * @var string
     *
     * @ORM\Column(type="integer")
     */
    protected $mpSessionId;

    /**
     * Date Placed
     *
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $datePlaced;

    /**
     * Date Completed
     *
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $dateCompleted = 0;


    /**
     * Get order ID
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set order ID
     */
    public function setOrderId($orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get Measurement Protocol Client ID
     */
    public function getMpClientId(): string
    {
        return $this->mpClientId;
    }

    /**
     * Set Measurement Protocol Client ID
     */
    public function setMpClientId(string $mpClientId): self
    {
        $this->mpClientId = $mpClientId;

        return $this;
    }

    /**
     * Get Measurement Protocol Session ID
     */
    public function getMpSessionId(): string
    {
        return $this->mpSessionId;
    }

    /**
     * Set Measurement Protocol Session ID
     */
    public function setMpSessionId(string $mpSessionId): self
    {
        $this->mpSessionId = $mpSessionId;

        return $this;
    }

    /**
     * Get date Placed
     */
    public function getDatePlaced()
    {
        return $this->datePlaced;
    }

    /**
     * Set date Placed
     */
    public function setDatePlaced($datePlaced): self
    {
        $this->datePlaced = $datePlaced;

        return $this;
    }

    /**
     * Get date Completed
     */
    public function getDateCompleted()
    {
        return $this->dateCompleted;
    }

    /**
     * Set date Completed
     */
    public function setDateCompleted($dateCompleted): self
    {
        $this->dateCompleted = $dateCompleted;

        return $this;
    }
}
