<?php
namespace lbs\models;

class Sandwich extends \Illuminate\Database\Eloquent\Model {

       protected $table      = 'sandwich';  /* le nom de la table */
       protected $primaryKey = 'id';     /* le nom de la clé primaire */
       public    $timestamps = false;    /* si vrai la table doit contenir
                                            les deux colonnes updated_at,
                                            created_at */
/*
	public function categories() {
		return $this->hasMany(	'lbs\models\Categorie',
								'sand2cat',
								'sand_id',
								'cat_id');
	}

	public function tailles() {
		return $this->hasMany(	'lbs\models\Taille',
								'tarif',
								'sand_id',
								'taille_id');
	}
*/
}
