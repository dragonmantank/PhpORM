# PhpORM Readme

Work on PhpORM has been moved to https://github.com/dragonmantank/casket and this repo will only be updated probably
never again. I'm leaving it here for some legacy projects I'm still using that are running an older version. I'd
highly suggest switching to casket.

[![Build Status](https://travis-ci.org/dragonmantank/PhpORM.svg?branch=master)](https://travis-ci.org/dragonmantank/PhpORM)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dragonmantank/PhpORM/badges/quality-score.png?s=e7ade4e389dafe0bb242333c3c9a7f246cfbc036)](https://scrutinizer-ci.com/g/dragonmantank/PhpORM/)

PhpORM is a compact Object Relational Management library for PHP. It allows for 
quick prototyping and management of objects that need to be persisted.

Since persistence doesn't always mean to a database, it can easily be extended
to support different persistance layers.

This version uses Aura.SQL to provide support for any database provider supported by Aura.SQL. All you need is PDO!

## Parts of PhpORM
* Entities - Singular Objects
* Repositories - Allows searching and retrieving
* Storage - Ways to access different data stores

### Entities
Entities are the 'things' in the application. If you are building a pet
website, Entities would be something like a 'Animal', 'AdoptionAgency',
'Address'. Entities are a single object that needs to be persisted, more
normally in a database.

An Entity would look like this:

    Class Animal
    {
        protected $id;           // Database ID
        protected $type;         // Type of animal
        protected $inductionDate // Date Animal came to Shelter
        protected $name;         // Name of the animal
        protected $shelter_id    // Foreign key of the animal shelter
    }

You could then access the entity like this:

    $animal = new Animal();
    $animal->type = 'Cat';
    $animal->inductionDate = time();
    $animalRepository->save($animal);

### Repositories
Repositories are the recommended way to get information out of the data storage. We've done away with the need for using
direct Data Access Objects. Repositories are generally set around a specific Entity and work only with that Entity. This
allows the Repository to automatically generate Entities based on the data sources.

For database-backed persistance, a basic repository needs no specific class and can utilize the shipped DBRepository class.
It requires just a storage mechanism and a prototype object to base it's results on.

    // $storage is a pre-created connection to your data store, see the Storage section
    $animalRepo = new PhpORM\Repository\DBRepository($storage, new Animal());

This repository will return Animal entities (or collections of Animal entities):

    $cats = $animalRepo->fetchAllBy(array('type' => 'cat'));  // Return all the cats
    $dog = $repo->find($dogId); // Find the specified object by primary key
    $fluffy = $repo->findBy(array('name' => 'Fluffy')); // Search by something other than primary key
  
### Storage
Storages replace the Data Access Objects of the older versions of PhpORM, as part of the DAO code now lives in the repositories.
Storage systems provide a standard way to interact with some sort of storage system, be it a database or an API. PhpORM
ships with a PDO-based storage system that utilizes Aura.SQL and Aura.SQL-Query to provide access to many different
database backends.

For the AuraExtendedPdo storage system, you just need to invoke it with an AuraExtendedPdo object and a QueryFactory from
Aura.SQL-Query.

    $storage = new PhpORM\Storage\AuraExtendedPdo($extendedPdo, new Aura\SqlQuery\QueryFactory('mysql'));

You can then use the storage with your Repositories so they can access data:

    $animalRepo = new PhpORM\Repository\DBRepository($storage, new Animal());
