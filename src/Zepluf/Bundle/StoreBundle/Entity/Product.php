<?php

namespace Zepluf\Bundle\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="Zepluf\Bundle\StoreBundle\Entity\ProductRepository")
 */
class Product
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="type", type="boolean", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="introduction_date", type="datetime", nullable=false)
     */
    private $introductionDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sales_discontinuation_date", type="datetime", nullable=true)
     */
    private $salesDiscontinuationDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="available_date", type="datetime", nullable=true)
     */
    private $availableDate;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="PriceComponent", inversedBy="product")
     * @ORM\JoinTable(name="product_price_component",
     *   joinColumns={
     *     @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="price_component_id", referencedColumnName="id")
     *   }
     * )
     */
    private $priceComponent;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="TermType", inversedBy="product")
     * @ORM\JoinTable(name="product_term",
     *   joinColumns={
     *     @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="term_type_id", referencedColumnName="id")
     *   }
     * )
     */
    private $termType;

    /**
     * @var \Organization
     *
     * @ORM\ManyToOne(targetEntity="Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="manufacturer_id", referencedColumnName="id")
     * })
     */
    private $manufacturer;

    /**
     * @var \UnitOfMeasurement
     *
     * @ORM\ManyToOne(targetEntity="UnitOfMeasurement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unit_of_measurement_id", referencedColumnName="id")
     * })
     */
    private $unitOfMeasurement;

    /**
     * @var \ProductFeatureApplication
     *
     * @ORM\OneToMany(targetEntity="ProductFeatureApplication", mappedBy="product")
     */
    private $productFeatureApplications;

    /**
     * @var \ProductFeatureApplication
     *
     * @ORM\OneToMany(targetEntity="ProductCategoryClassification", mappedBy="product")
     */
    private $productCategoryClassifications;

    //...
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->priceComponent = new \Doctrine\Common\Collections\ArrayCollection();
        $this->termType = new \Doctrine\Common\Collections\ArrayCollection();
        $this->productFeatureApplications = new \Doctrine\Common\Collections\ArrayCollection();
        $this->productCategoryClassifications = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param boolean $type
     * @return Product
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return boolean
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set introductionDate
     *
     * @param \DateTime $introductionDate
     * @return Product
     */
    public function setIntroductionDate($introductionDate)
    {
        $this->introductionDate = $introductionDate;

        return $this;
    }

    /**
     * Get introductionDate
     *
     * @return \DateTime
     */
    public function getIntroductionDate()
    {
        return $this->introductionDate;
    }

    /**
     * Set salesDiscontinuationDate
     *
     * @param \DateTime $salesDiscontinuationDate
     * @return Product
     */
    public function setSalesDiscontinuationDate($salesDiscontinuationDate)
    {
        $this->salesDiscontinuationDate = $salesDiscontinuationDate;

        return $this;
    }

    /**
     * Get salesDiscontinuationDate
     *
     * @return \DateTime
     */
    public function getSalesDiscontinuationDate()
    {
        return $this->salesDiscontinuationDate;
    }

    /**
     * Set availableDate
     *
     * @param \DateTime $availableDate
     * @return Product
     */
    public function setAvailableDate($availableDate)
    {
        $this->availableDate = $availableDate;

        return $this;
    }

    /**
     * Get availableDate
     *
     * @return \DateTime
     */
    public function getAvailableDate()
    {
        return $this->availableDate;
    }

    /**
     * Add priceComponent
     *
     * @param \Zepluf\Bundle\StoreBundle\Entity\PriceComponent $priceComponent
     * @return Product
     */
    public function addPriceComponent(\Zepluf\Bundle\StoreBundle\Entity\PriceComponent $priceComponent)
    {
        $this->priceComponent[] = $priceComponent;

        return $this;
    }

    /**
     * Remove priceComponent
     *
     * @param \Zepluf\Bundle\StoreBundle\Entity\PriceComponent $priceComponent
     */
    public function removePriceComponent(\Zepluf\Bundle\StoreBundle\Entity\PriceComponent $priceComponent)
    {
        $this->priceComponent->removeElement($priceComponent);
    }

    /**
     * Get priceComponent
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPriceComponent()
    {
        return $this->priceComponent;
    }

    /**
     * Add termType
     *
     * @param \Zepluf\Bundle\StoreBundle\Entity\TermType $termType
     * @return Product
     */
    public function addTermType(\Zepluf\Bundle\StoreBundle\Entity\TermType $termType)
    {
        $this->termType[] = $termType;

        return $this;
    }

    /**
     * Remove termType
     *
     * @param \Zepluf\Bundle\StoreBundle\Entity\TermType $termType
     */
    public function removeTermType(\Zepluf\Bundle\StoreBundle\Entity\TermType $termType)
    {
        $this->termType->removeElement($termType);
    }

    /**
     * Get termType
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTermType()
    {
        return $this->termType;
    }

    /**
     * Set manufacturer
     *
     * @param \Zepluf\Bundle\StoreBundle\Entity\Organization $manufacturer
     * @return Product
     */
    public function setManufacturer(\Zepluf\Bundle\StoreBundle\Entity\Organization $manufacturer = null)
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * Get manufacturer
     *
     * @return \Zepluf\Bundle\StoreBundle\Entity\Organization
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * Set unitOfMeasurement
     *
     * @param \Zepluf\Bundle\StoreBundle\Entity\UnitOfMeasurement $unitOfMeasurement
     * @return Product
     */
    public function setUnitOfMeasurement(\Zepluf\Bundle\StoreBundle\Entity\UnitOfMeasurement $unitOfMeasurement = null)
    {
        $this->unitOfMeasurement = $unitOfMeasurement;

        return $this;
    }

    /**
     * Get unitOfMeasurement
     *
     * @return \Zepluf\Bundle\StoreBundle\Entity\UnitOfMeasurement
     */
    public function getUnitOfMeasurement()
    {
        return $this->unitOfMeasurement;
    }

    /**
     * Add productFeatureApplications
     *
     * @param \Zepluf\Bundle\StoreBundle\Entity\ProductFeatureApplication $productFeatureApplications
     * @return Product
     */
    public function addProductFeatureApplication(\Zepluf\Bundle\StoreBundle\Entity\ProductFeatureApplication $productFeatureApplications)
    {
        $this->productFeatureApplications[] = $productFeatureApplications;

        return $this;
    }

    /**
     * Remove productFeatureApplications
     *
     * @param \Zepluf\Bundle\StoreBundle\Entity\ProductFeatureApplication $productFeatureApplications
     */
    public function removeProductFeatureApplication(\Zepluf\Bundle\StoreBundle\Entity\ProductFeatureApplication $productFeatureApplications)
    {
        $this->productFeatureApplications->removeElement($productFeatureApplications);
    }

    /**
     * Get productFeatureApplications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductFeatureApplications()
    {
        return $this->productFeatureApplications;
    }

    /**
     * Add productCategoryClassifications
     *
     * @param \Zepluf\Bundle\StoreBundle\Entity\ProductCategoryClassification $productCategoryClassifications
     * @return Product
     */
    public function addProductCategoryClassification(\Zepluf\Bundle\StoreBundle\Entity\ProductCategoryClassification $productCategoryClassifications)
    {
        $this->productCategoryClassifications[] = $productCategoryClassifications;

        return $this;
    }

    /**
     * Remove productCategoryClassifications
     *
     * @param \Zepluf\Bundle\StoreBundle\Entity\ProductCategoryClassification $productCategoryClassifications
     */
    public function removeProductCategoryClassification(\Zepluf\Bundle\StoreBundle\Entity\ProductCategoryClassification $productCategoryClassifications)
    {
        $this->productCategoryClassifications->removeElement($productCategoryClassifications);
    }

    /**
     * Get productCategoryClassifications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductCategoryClassifications()
    {
        return $this->productCategoryClassifications;
    }
}