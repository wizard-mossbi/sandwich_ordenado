<?php
namespace lbs\models;

class Categorie extends \Illuminate\Database\Eloquent\Model {

       protected $table      = 'categorie';  /* le nom de la table */
       protected $primaryKey = 'id';     /* le nom de la clé primaire */
       public    $timestamps = false;    /* si vrai la table doit contenir
                                            les deux colonnes updated_at,
                                            created_at */
	/*
	public function sandwiches() {
		return $this->hasMany(	'lbs\models\Sandwich',
								'sand2cat',
								'cat_id',
								'sand_id');
	}
*/

}
