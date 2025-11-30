<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    protected $user;
    protected $users = [];
    protected $type = 'system';
    protected $priority = 'normal';
    protected $title;
    protected $message;
    protected $additionalData = [];
    protected $roles = [];

    /**
     * Set the recipient user
     */
    public function toUser($user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Set multiple recipient users
     */
    public function toUsers(Collection $users): self
    {
        $this->users = $users;
        return $this;
    }

    /**
     * Set roles to send to
     */
    public function toRole($roles): self
    {
        $this->roles = (array) $roles;
        return $this;
    }

    /**
     * Set the notification type
     */
    public function type(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Set the notification priority
     */
    public function priority(string $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Set the notification title
     */
    public function title(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set the notification message
     */
    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Set additional data for the notification
     */
    public function with(array $data): self
    {
        $this->additionalData = array_merge($this->additionalData, $data);
        return $this;
    }

    /**
     * Send notification based on configuration
     */
    public function send()
    {
        if (!$this->title || !$this->message) {
            throw new \Exception("Notification title and message are required");
        }

        // Determine recipients
        if (!empty($this->roles)) {
            return $this->sendToRoles($this->roles);
        }

        if (!empty($this->users)) {
            return $this->sendToUsers($this->users);
        }

        if ($this->user) {
            return $this->sendToUser($this->user);
        }

        throw new \Exception("No notification recipients specified");
    }

    /**
     * Send to a single user (internal)
     */
    protected function sendToUser($user): Notification
    {
        $notificationData = [
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'priority' => $this->priority,
            'user_id' => $user instanceof User ? $user->id : $user,
            'is_read' => false,
        ];

        $notificationData = array_merge($notificationData, $this->additionalData);

        return Notification::create($notificationData);
    }

    /**
     * Send to multiple users (internal)
     */
    protected function sendToUsers(Collection $users): array
    {
        $sentNotifications = [];
        
        foreach ($users as $user) {
            $sentNotifications[] = $this->sendToUser($user);
        }

        return $sentNotifications;
    }

    /**
     * Send to roles (internal)
     */
    protected function sendToRoles(array $roles): array
    {
        $users = User::role($roles)->get();
        return $this->sendToUsers($users);
    }

    /**
     * Send to roles - NEW METHOD that accepts parameters
     */
    public function sendToRole($roles, string $title = null, string $message = null): array
    {
        // If title and message are provided as parameters, use them
        if ($title) {
            $this->title = $title;
        }
        if ($message) {
            $this->message = $message;
        }

        $this->roles = (array) $roles;
        
        $result = $this->send();
        $this->reset();
        return $result;
    }

    /**
     * Send to all users
     */
    public function sendToAll(string $title = null, string $message = null): array
    {
        if ($title) {
            $this->title = $title;
        }
        if ($message) {
            $this->message = $message;
        }

        $this->users = User::all();
        $result = $this->send();
        $this->reset();
        return $result;
    }

    /**
     * Quick methods for common notification types
     */
    public function alert(string $title, string $message, $user = null)
    {
        $this->type('alert')->title($title)->message($message);
        
        if ($user) {
            $this->toUser($user);
            $result = $this->send();
        } else {
            $result = $this->send();
        }
        
        $this->reset();
        return $result;
    }

    public function payment(string $title, string $message, $user = null)
    {
        $this->type('payment')->title($title)->message($message);
        
        if ($user) {
            $this->toUser($user);
            $result = $this->send();
        } else {
            $result = $this->send();
        }
        
        $this->reset();
        return $result;
    }

    public function order(string $title, string $message, $user = null)
    {
        $this->type('order')->title($title)->message($message);
        
        if ($user) {
            $this->toUser($user);
            $result = $this->send();
        } else {
            $result = $this->send();
        }
        
        $this->reset();
        return $result;
    }

    public function production(string $title, string $message, $user = null)
    {
        $this->type('production')->title($title)->message($message);
        
        if ($user) {
            $this->toUser($user);
            $result = $this->send();
        } else {
            $result = $this->send();
        }
        
        $this->reset();
        return $result;
    }

    /**
     * Reset the service state
     */
    protected function reset(): void
    {
        $this->user = null;
        $this->users = [];
        $this->roles = [];
        $this->type = 'system';
        $this->priority = 'normal';
        $this->title = null;
        $this->message = null;
        $this->additionalData = [];
    }
}