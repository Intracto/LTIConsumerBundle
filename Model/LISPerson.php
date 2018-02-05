<?php

namespace Intracto\LTIConsumerBundle\Model;

use Symfony\Component\HttpFoundation\Request;

class LISPerson
{
    private $familyName;

    private $name;

    private $userId;

    private $role;

    private $email;

    private $sourceId;

    /**
     * LISPerson constructor.
     *
     * @param $familyName
     * @param $name
     * @param $userId
     * @param $role
     * @param $email
     * @param $sourceId
     */
    public function __construct($familyName, $name, $userId, $role, $email, $sourceId)
    {
        $this->familyName = $familyName;
        $this->name = $name;
        $this->userId = $userId;
        $this->role = $role;
        $this->email = $email;
        $this->sourceId = $sourceId;
    }

    public static function createFromRequest(Request $request)
    {
        return new self(
            $request->query->getAlnum('familyName'),
            $request->query->getalnum('name'),
            $request->query->getAlnum('userId'),
            $request->query->getAlnum('role', 'Learner'),
            $request->query->get('email'),
            $request->query->get('sourceId')
        );
    }

    public function getAsParameters()
    {
        return $parameters = array(
            'lis_person_name_family' => $this->familyName,
            'lis_person_name_given' => $this->name,
            'lis_person_contact_email_primary' => $this->email,
            'lis_person_sourcedid' => $this->sourceId,
            'roles' => $this->role,
            'user_id' => $this->userId,
        );
    }

    /**
     * @return mixed
     */
    public function getFullname()
    {
        return $this->name.' '.$this->familyName;
    }

    /**
     * @return mixed
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * @param mixed $familyName
     */
    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * @param mixed $sourceId
     */
    public function setSourceId($sourceId)
    {
        $this->sourceId = $sourceId;
    }
}
