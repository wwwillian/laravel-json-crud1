<?php

namespace Wwwillian\JsonCrud\Providers;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class EloquentUserProvider implements UserProvider
{
    /**
     * The hasher implementation.
     *
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * The Eloquent user model.
     *
     * @var string
     */
    protected $model;

    /**
     * The Eloquent model that validates user and password.
     *
     * @var string
     */
    protected $credentialModel;

    /**
     * The Email model that contains the emails associated with credential.
     *
     * @var string
     */
    protected $emailModel;

    /**
     * The foreign key column in Eloquent Model.
     *
     * @var string
     */
    protected $foreignKey;

    /**
     * Create a new database user provider.
     *
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @param  string  $model
     * @return void
     */
    public function __construct(HasherContract $hasher, $model, $emailModel, $credentialModel, $foreignKey)
    {
        $this->hasher          = $hasher;
        $this->model           = $model;
        $this->emailModel      = $emailModel;
        $this->credentialModel = $credentialModel;
        $this->foreignKey      = $foreignKey;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        $model = $this->createModel();

        return $model->newQuery()
            ->where($model->getAuthIdentifierName(), $identifier)
            ->first();
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $model = $this->createModel();

        $model = $model->where($model->getAuthIdentifierName(), $identifier)->first();

        if (!$model) {
            return null;
        }

        $rememberToken = $model->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token) ? $model : null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|\Illuminate\Database\Eloquent\Model  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(UserContract $user, $token)
    {
        $user->setRememberToken($token);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModel()
    {
        $class = '\\' . ltrim($this->model, '\\');

        return new $class;
    }

    /**
     * Gets the hasher implementation.
     *
     * @return \Illuminate\Contracts\Hashing\Hasher
     */
    public function getHasher()
    {
        return $this->hasher;
    }

    /**
     * Sets the hasher implementation.
     *
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @return $this
     */
    public function setHasher(HasherContract $hasher)
    {
        $this->hasher = $hasher;

        return $this;
    }

    /**
     * Gets the name of the Eloquent user model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets the name of the Eloquent user model.
     *
     * @param  string  $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Create a new instance of credential model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createCredentialModel()
    {
        $class = '\\' . ltrim($this->credentialModel, '\\');

        return new $class;
    }

    /**
     * Gets the name of the credential model.
     *
     * @return string
     */
    public function getCredentialModel()
    {
        return $this->credentialModel;
    }

    /**
     * Sets the name of the credential model.
     *
     * @param  string  $model
     * @return $this
     */
    public function setCredentialModel($model)
    {
        $this->credentialModel = $model;

        return $this;
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createEmailModel()
    {
        $class = '\\' . ltrim($this->emailModel, '\\');

        return new $class;
    }

    /**
     * Gets the name of the Eloquent user model.
     *
     * @return string
     */
    public function getEmailModel()
    {
        return $this->emailModel;
    }

    /**
     * Sets the name of the Eloquent user model.
     *
     * @param  string  $model
     * @return $this
     */
    public function setEmailModel($model)
    {
        $this->emailModel = $model;

        return $this;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) || (count($credentials) === 1 &&
            array_key_exists('password', $credentials))) {
            return;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $credentialModel = $this->createCredentialModel();
        $emailModel      = $this->createEmailModel();
        $model           = $this->createModel();

        $query           = $model->newQuery();
        $credentialQuery = $credentialModel->newQuery();
        $emailQuery      = $emailModel->newQuery();

        $emailModel      = $emailQuery->where('email', $credentials['user'])->first();
        $credentialModel = $credentialQuery->where('username', $credentials['user'])->first();

        if (isset($emailModel)) {
            $query->where($this->foreignKey, $emailModel->credential_id);
            return $query->first();
        } else if (isset($credentialModel)) {
            $query->where($this->foreignKey, $credentialModel->id);
            return $query->first();
        } else {
            return null;
        }
    }
}
