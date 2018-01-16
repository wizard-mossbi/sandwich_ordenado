<?php
namespace lbs\models;

class Commande extends \Illuminate\Database\Eloquent\Model {

       protected $table      = 'commande';  /* le nom de la table */
       protected $primaryKey = 'id';     /* le nom de la clé primaire */
       public $incrementing = false;
       public $keyType = 'string';
       public $timestamps = false;

	/*
	public function sandwiches() {
		return $this->hasMany(	'lbs\models\Sandwich',
								'sand2cat',
								'cat_id',
								'sand_id');
	}
*/

}
