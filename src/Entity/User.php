<?php

namespace App\Entity;
use App\Entity\Role;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use DateTimeInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("email")
 */
class User implements UserInterface, \Serializable
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("conversation")
     * @Groups("users")
     * 
     */
    private $id;

    /**
     * @ORM\Column(type="string",length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *  min=2,
     *  max=30,
     * minMessage="Your First Name  must be at least {{ limit }} characters long",
     * maxMessage="Your First Name  cannot be longer than {{ limit }} characters"
     * )
     *   @Groups("user")
     * @Groups("conversation")
     *  @Groups("users")
     *
     */
    private $firstName;

    /**
     * @ORM\Column(type="string",length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *  min=2,
     *  max=50,
     * minMessage="Your Last Name  must be at least {{ limit }} characters long",
     * maxMessage="Your Last Name  cannot be longer than {{ limit }} characters"
     * )
     * @Groups("user")
     * @Groups("conversation")
     *  @Groups("users")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string",length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Assert\Length(
     *  min=3,
     *  max=255,
     * minMessage="Your Last Name  must be at least {{ limit }} characters long",
     * maxMessage="Your Last Name  cannot be longer than {{ limit }} characters"
     * )
     * @Groups("user")
     * @Groups("conversation")
     *  @Groups("users")
     */
    private $email;


    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     * @Groups("conversation")
     *
     */
    private $createAt;

  

    /**
     * @ORM\Column(type="string",length=255)
     * @Assert\NotBlank()
      * @Assert\Regex(
     *   pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *   message="Password must be seven charachters and contain at least one digit,one uppercase and one lowercase"
     * )
     */
    private $password;

     /**
     *  @Assert\EqualTo(
     *   propertyPath="password",
     *     message="Passwords does not match"
     * )
     */

    private $confirmPassword;

   


 
   

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", mappedBy="users")
     */
    private $userRoles;

    private $roles;

     /**
     * @ORM\Column(type="boolean")
     * @Groups("conversation")
     */
    private $enabled;



 /**
     * @ORM\OneToMany(targetEntity="Participant", mappedBy="user")
     */
    private $participants;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="user")
     */
    private $messages;

    
/**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $lastActivityAt;

/**
 * @param \Datetime $lastActivityAt
 */
public function setLastActivityAt($lastActivityAt)
{
    $this->lastActivityAt = $lastActivityAt;
}

/**
 * @return \Datetime
 */
public function getLastActivityAt()
{
    return $this->lastActivityAt;
}

/**
 * @return Bool Whether the user is active or not
 */
public function isActiveNow()
{
    // Delay during wich the user will be considered as still active
    $delay = new \DateTime('2 minutes ago');

    return ( $this->getLastActivityAt() > $delay );
}




    public function __construct()
    {
       
        $this->userRoles=new ArrayCollection();
        $this->enabled=true;

        
        $this->participants = new ArrayCollection();
        $this->messages = new ArrayCollection();
    
   
  
  
   
    }

    /**
     * 
     */

    public function getId(){
        return $this->id;
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Undocumented function
     *
     * @param string $firstName
     * @return self
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }
  
    /**
     * 
     */

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getFullName():?string
    {
        return $this->firstName.' '.$this->lastName ;
    }

    /**
     * Undocumented function
     *
     * @param string $lastName
     * @return self
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        
        return $this->email;
    }

    /**
     * Undocumented function
     *
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }




    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        
        return $this->password;
    }

    /**
     * Undocumented function
     *
     * @param string $password
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    /**
     * Undocumented function
     *
     * @param string $confirmPassword
     * @return self
     */
    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }

    

    ///////////////////////////////////////////////////
    
    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt(){  return null;}

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername(){
    
        return $this->email;
    }

    public function setUsername(string $email):self
    {
         $this->username=$email;
         return $this;
    }
    public function fullName(){
        return " {$this->firstName} {$this->lastName} ";
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(){}

    public function getRoles(){
        $roles=  $this->userRoles->map(function($role){
             return $role->getName();
         })->toArray();
 
          $roles[]='ROLE_USER';
        
         
         return $roles;
     }

    ///////////////////////////////////////////
 
    /////////////////////////////////////////
      /**
     * @return Collection|Role[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    /**
     * Undocumented function
     *
     * @param Role $userRole
     * @return self
     */
    public function addUserRole(Role $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
            $userRole->addUser($this);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param Role $userRole
     * @return self
     */
    public function removeUserRole(Role $userRole): self
    {
        if ($this->userRoles->contains($userRole)) {
            $this->userRoles->removeElement($userRole);
            $userRole->removeUser($this);
        }

        return $this;
    }





    
   
 

    ///////////////////////////////////////////

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function initDate(){
        $this->createAt=new  \DateTime();
    }

    /**
     * Undocumented function
     *
     * @return \DateTimeInterface|null
     */
    public function getCreateAt(): ?\DateTimeInterface 
    {
        return $this->createAt;
    }

   /**
    * Undocumented function
    *
    * @param \DateTimeInterface $createAt
    * @return self
    */
    public function setCreateAt(\DateTimeInterface $createAt):self
    {
        $this->createAt = $createAt;

        return $this;
    }

   
   
 


   public function serialize()
   {
       return serialize(
           [
               $this->id,
               $this->firstName,
               $this->lastName,
               $this->email,
               $this->password,
               $this->enabled
          
            
           ]
       );
   }

   public function unserialize($serialized)
   {
       list(
           $this->id,  
           $this->firstName,
           $this->lastName,
           $this->email, 
           $this->password,
           $this->enabled
      
           ) = unserialize($serialized);
   }



    /**
     * Get the value of enabled
     */ 
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set the value of enabled
     *
     * @return  self
     */ 
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function __toString()
    {
        return $this->getFullName();
    }

 
/**
     * @return Collection|Participant[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
            $participant->setUser($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
            // set the owning side to null (unless already changed)
            if ($participant->getUser() === $this) {
                $participant->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setUser($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getUser() === $this) {
                $message->setUser(null);
            }
        }

        return $this;
    }



}