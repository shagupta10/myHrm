<?php

/**
 * TrainingTrainerTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class TrainingTrainerTable extends PluginTrainingTrainerTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object TrainingTrainerTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('TrainingTrainer');
    }
}