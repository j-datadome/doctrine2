<?php

namespace Doctrine\Tests\Models\DDC964;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\JoinColumnMetadata;

/**
 * @MappedSuperclass
 */
class DDC964User
{

    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer", name="user_id")
     */
    protected $id;

    /**
     * @Column(name="user_name", nullable=true, unique=false, length=250)
     */
    protected $name;

    /**
     * @var ArrayCollection
     *
     * @ManyToMany(targetEntity="DDC964Group", inversedBy="users", cascade={"persist", "merge", "detach"})
     * @JoinTable(name="ddc964_users_groups",
     *  joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *  inverseJoinColumns={@JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * @var DDC964Address
     *
     * @ManyToOne(targetEntity="DDC964Address", cascade={"persist", "merge"})
     * @JoinColumn(name="address_id", referencedColumnName="id")
     */
    protected $address;

    /**
     * @param string $name
     */
    public function __construct($name = null)
    {
        $this->name     = $name;
        $this->groups   = new ArrayCollection;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param DDC964Group $group
     */
    public function addGroup(DDC964Group $group)
    {
        $this->groups->add($group);
        $group->addUser($this);
    }

    /**
     * @return ArrayCollection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @return DDC964Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param DDC964Address $address
     */
    public function setAddress(DDC964Address $address)
    {
        $this->address = $address;
    }

    public static function loadMetadata(\Doctrine\ORM\Mapping\ClassMetadata $metadata)
    {
        $metadata->addProperty('id', Type::getType('integer'), array(
           'id'         => true,
           'columnName' => 'user_id',
        ));

        $metadata->addProperty('name', Type::getType('string'), array(
            'columnName'=> 'user_name',
            'nullable'  => true,
            'unique'    => false,
            'length'    => 250,
        ));

        $joinColumns = array();

        $joinColumn = new JoinColumnMetadata();

        $joinColumn->setColumnName('address_id');
        $joinColumn->setReferencedColumnName('id');

        $joinColumns[] = $joinColumn;

        $metadata->mapManyToOne(array(
           'fieldName'      => 'address',
           'targetEntity'   => 'DDC964Address',
           'cascade'        => array('persist','merge'),
           'joinColumns'    => $joinColumns,
        ));

        $joinColumns = $inverseJoinColumns = array();

        $joinColumn = new JoinColumnMetadata();

        $joinColumn->setColumnName('user_id');
        $joinColumn->setReferencedColumnName('id');

        $joinColumns[] = $joinColumn;

        $joinColumn = new JoinColumnMetadata();

        $joinColumn->setColumnName('group_id');
        $joinColumn->setReferencedColumnName('id');

        $inverseJoinColumns[] = $joinColumn;

        $joinTable = array(
            'name'               => 'ddc964_users_groups',
            'joinColumns'        => $joinColumns,
            'inverseJoinColumns' => $inverseJoinColumns,
        );

        $metadata->mapManyToMany(array(
           'fieldName'    => 'groups',
           'targetEntity' => 'DDC964Group',
           'inversedBy'   => 'users',
           'cascade'      => array('persist','merge','detach'),
           'joinTable'    => $joinTable,
        ));

        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_AUTO);
    }
}