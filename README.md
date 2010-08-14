# PhpORM Readme
Last Updated: 2010-08-13, Chris Tankersley

PhpORM is a compact Object Relational Management library for PHP. It allows for 
quick prototyping and management of objects that need to be persisted.

Since persistence doesn't always mean to a database, it can easily be extended
to support different persistance layers.

It works out of the box with Zend Framework by integrating Zend_Db and by itself.

## Types of Objects
* Entities - Singular Objects
* Repositories - Allows searching and retrieving
* Collections - Groups of similar Entities
* DAOs - Access methods for storage

### Entities
Entities are the 'things' in the application. If you are building a pet
website, Entities would be something like a 'Animal', 'AdoptionAgency',
'Address'. Entities are a single object that needs to be persisted, more
normally in a database.

An Entity would look like this:

    Class Animal extends PhpORM_Entity
    {
        // Reserved entity members are prefixed with a _
        protected $_daoObjectName = 'Dao_Animals'; // Class to use for data access

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
    $animal['name'] = 'Fluffy';
    $animal->save();

#### Relationships
Entities also have a concept of relationships, in either the one-to-many or
the one-to-one setup. You define relationships and the Entity will load them
as needed to cut down on data access. 

If we wanted to associate 

    Class Animal extends PhpORM_Entity
    {
        ....
        protected $_relationships = array(
            'AnimalShelter' => array(
                'repo' => 'Repository_Shelters',
                'entity' => 'Shelter',
                'key' => array('foreign' => 'id', 'local' => 'shelter_id'),
                'type' => 'one', 
            ),
        );
    }

And you would access the relationship like this:

    $shelter = $animal->AnimalShelter;

### Repositories
Repositories are the recommended way to get information out of the data storage. While you can use
the DAOs directly, the Repositories allow more flexibility. Repositories are generally set around a specific Entity and work only with that Entity. This allows the Repository to automatically generate Entities based
on the data sources.

A basic Repository looks like this:

    class Repository_Animals extends PhpORM_Repository
    {
        protected $_daoObjectName = 'Dao_Animals';
        protected $_entityName = 'Animal';
    }

This repository will return Animal entities (or collections of Animal entities):

    $repo = new Repository_Animals();
    $cats = $repo->fetchAllBy('type', 'cat');  // Return all the cats
    $dog = $repo->find($dogId); // Find the specified object by primary key
    $fluffy = $repo->findOneBy('name', 'Fluffy'); // Search by something other than primary key

### Collections
Collections are a group of entities. They provide storage as well as additional searching capabilities. Collections can be created manually or can be returned by Repositories. They can be manipulated like
PHP arrays or like objects, and contain basic searching functions.

    // Select all the animals in a shelter
    $collection = $repo->fetchAllBy('shelter_id', $shelter->id);
    // Pull out all of the cats without going back to the data source
    $cats = $collection->fetchAllBy('type', 'cats');
    // Add a new cat
    $collection[] = $cat
    // Find a specific cat without knowing the primary key
    $fluffy = $collection->findOneBy('name', 'Fluffy');
  
### DAOs (Data Access Objects)
DAOs take care of the actual persistence of Entities. These directly interact with something like a database, CSV file, XML, or even something not truly persistent like an Array. Since DAOs are meant to be generic, they always return an array. For more string typecasting, the Repository is a better access source.

Each DAO type needs to extend the PhpORM\_Dao abstract class an implement the functions. An example for Database access is using PhpORM\_Dao\_ZendDb to use the Zend_Db methods. 

    class Dao_Animals extends PhpORM_Dao_ZendDb
    {
        protected $_tableName = 'a_Animals';
    }

    $dao = new Dao_Animals();
    $cats = $dao->fetchAllBy('type', 'cat');
    is_array($cats); // Would return true

## Prototyping
PhpORM also contains two things to make prototyping code quick and easy. PhpORM\_Dao\_ArrayStorage can be used to quickly populate a data storage from a simple PHP array and looks no different than if you were doing database access:

There is also a generic Entity class in the form of PhpORM\_Entity\_Generic. It will accept any set of attributes and can be created from arrays as well. 

    $dao = new PhpORM_Dao_ArrayStorage();
    $dao->insert(array('id'=>1,'type'=>'cat','name'=>'Fluffy'));
    $dao->insert(array('id'=>2,'type'=>'cat','name'=>'Missy'));
    $dao->insert(array('id'=>3,'type'=>'dog','name'=>'Rex'));

    $collection = new PhpORM_Collection();
    foreach($dao->fetchAllBy('type', 'cats') as $row) {
        $collection[] = new PhpORM_Entity_Generic($row);
    }
    $fluffy = $collection->fetchOneBy('name', 'Fluffy');
    $fluffy->setDao($dao); // We want to override the built-in DAO
    $fluffy->shelter_id = 5;
    $fluffy->save(); // This will save it back to the ArrayStorage DAO
