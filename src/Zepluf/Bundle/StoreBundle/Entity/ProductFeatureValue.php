<?php

namespace Zepluf\Bundle\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductFeatureValue
 *
 * @ORM\Table(name="product_feature_value")
 * @ORM\Entity
 */
class ProductFeatureValue
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
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=false)
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
<<<<<<< HEAD
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="ProductFeature", mappedBy="productFeatureValue")
     */
    private $productFeature;
=======
     * @var \ProductFeature
     *
     * @ORM\ManyToOne(targetEntity="ProductFeature")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_feature_id", referencedColumnName="id")
     * })
     */
    private $productFeature;

>>>>>>> 9cda9ba8825f6f9dbff13b0b3726e1d596b3262c

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productFeature = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set value
     *
     * @param string $value
     * @return ProductFeatureValue
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ProductFeatureValue
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
<<<<<<< HEAD
     * Add productFeature
=======
     * Set productFeature
>>>>>>> 9cda9ba8825f6f9dbff13b0b3726e1d596b3262c
     *
     * @param \Zepluf\Bundle\StoreBundle\Entity\ProductFeature $productFeature
     * @return ProductFeatureValue
     */
<<<<<<< HEAD
    public function addProductFeature(\Zepluf\Bundle\StoreBundle\Entity\ProductFeature $productFeature)
    {
        $this->productFeature[] = $productFeature;
=======
    public function setProductFeature(\Zepluf\Bundle\StoreBundle\Entity\ProductFeature $productFeature = null)
    {
        $this->productFeature = $productFeature;
>>>>>>> 9cda9ba8825f6f9dbff13b0b3726e1d596b3262c
    
        return $this;
    }

    /**
<<<<<<< HEAD
     * Remove productFeature
     *
     * @param \Zepluf\Bundle\StoreBundle\Entity\ProductFeature $productFeature
     */
    public function removeProductFeature(\Zepluf\Bundle\StoreBundle\Entity\ProductFeature $productFeature)
    {
        $this->productFeature->removeElement($productFeature);
    }

    /**
     * Get productFeature
     *
     * @return \Doctrine\Common\Collections\Collection 
=======
     * Get productFeature
     *
     * @return \Zepluf\Bundle\StoreBundle\Entity\ProductFeature 
>>>>>>> 9cda9ba8825f6f9dbff13b0b3726e1d596b3262c
     */
    public function getProductFeature()
    {
        return $this->productFeature;
    }
}