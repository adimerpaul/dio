<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;
class JuventudTaller extends Model implements AuditableContract
{
    use AuditableTrait, SoftDeletes;
    //        Schema::create('juventud_talleres', function (Blueprint $table) {
    //            $table->id();
    //            $table->string('actividad', 160);
    //            $table->string('area', 120)->nullable();
    //            $table->text('descripcion')->nullable();
    //            $table->string('poblacion', 120)->nullable();
    //            $table->string('lugar', 160)->nullable();
    //            $table->string('responsables', 160)->nullable();
    //            $table->string('colaboracion', 160)->nullable();
    //            $table->text('observaciones')->nullable();
    //            $table->dateTime('fecha_inicio');
    //            $table->dateTime('fecha_final')->nullable();
    //            $table->string('color', 7)->nullable();
    //            $table->unsignedBigInteger('user_id')->nullable();
    //            $table->foreign('user_id')->references('id')->on('users');
    //            $table->softDeletes();
    //            $table->timestamps();
    //        });
    protected $table = 'juventud_talleres';
    protected $fillable = [
        'actividad',
        'area',
        'descripcion',
        'poblacion',
        'lugar',
        'responsables',
        'colaboracion',
        'observaciones',
        'fecha_inicio',
        'fecha_final',
        'color',
        'user_id',
    ];
    protected  $hidden = ['created_at', 'updated_at', 'deleted_at'];

    function user(){
        return $this->belongsTo(User::class);
    }
}
