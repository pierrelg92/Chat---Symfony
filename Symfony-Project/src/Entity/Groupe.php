<?php

namespace App\Entity;

use App\Entity\Message;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupeRepository")
 */
class Groupe 
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $photo = 'defaultGroup.png';
    // Par défaut, si on ne met pas d'image, on ira chercher cette l'image 'default.jpg'.

    private $file;
    //Cette propieté va correspondre au fichier uploader dans le formulaire donc as besoin de la mapper.

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="groups")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="groupe")
     */
    private $messages;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
        return $this;
    }

    //Fonction pour gérer l'upload des images: renomer l'image en BDD, enregistrer l'image en BDD.
    public function fileUpload()
    {
        $newName = $this->renameFile($this-> file-> getClientOriginalName());

        $this-> photo = $newName;

        $this-> file->move(__DIR__ . '/../../public/img/groups/', $newName);

    }

    public function renameFile($name)
    {
        return 'fichier_' . time() . '_' . rand(1, 9999) . '_' . $name;
    }

    public function removeFile()
    {
        if(file_exists(__DIR__ . '/../../public/img/groups/' . $this-> photo) && $this-> photo != 'default.jpg')
        {
            unlink(__DIR__ . '/../../public/img/groups/' . $this-> photo);
        }
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addGroup($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeGroup($this);
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
            $message->setGroupe($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getGroupe() === $this) {
                $message->setGroupe(null);
            }
        }

        return $this;
    }
}
