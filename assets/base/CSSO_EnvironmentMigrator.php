<?php

namespace CloudSingleSignOn\base;

abstract class CSSO_MigrateOptions
{
    const  ReplaceIfExist = 'replace_if_exist' ;
    const  WithoutReplace = 'without_replace' ;
    const  AttributeMapping = 'attribute_mapping' ;
}
class CSSO_EnvironmentMigrator
{
    private  $source_env_id ;
    private  $target_env_id ;
    private  $source_env_config ;
    private  $target_env_config ;
    private  $migrate_option ;
    private  $migrate_options ;
    /**
     * @var CSSO_ProvidersManager
     */
    private  $providers_manager ;
    public function __construct( $source_env_id, $target_env_id )
    {
        global  $providers_manager ;
        $this->providers_manager = $providers_manager;
        $this->source_env_id = $source_env_id;
        $this->target_env_id = $target_env_id;
        $this->source_env_config = $providers_manager->csso_get_config_by_env_id( $source_env_id );
        $this->target_env_config = $providers_manager->csso_get_config_by_env_id( $target_env_id );
        $this->csso_set_migrate_options();
    }
    
    private function csso_set_migrate_options()
    {
        $this->migrate_options = [
            CSSO_MigrateOptions::ReplaceIfExist   => function () {
            $this->csso_migrate_with_replace();
        },
            CSSO_MigrateOptions::WithoutReplace   => function () {
            $this->csso_migrate_without_replace();
        },
            CSSO_MigrateOptions::AttributeMapping => function () {
            $this->csso_migrate_only_mapping();
        },
        ];
    }
    
    private function csso_migrate_with_replace()
    {
        foreach ( $this->source_env_config as $provider ) {
            $existing_provider = csso_search_in_array( $this->target_env_config, 'provider', $provider['provider'] );
            
            if ( $existing_provider ) {
                $provider['id'] = $existing_provider['id'];
            } else {
                unset( $provider['id'] );
            }
            
            $provider['env_id'] = $this->target_env_id;
            $this->providers_manager->csso_create_or_replace( $provider );
        }
    }
    
    private function csso_migrate_without_replace()
    {
        foreach ( $this->source_env_config as $provider ) {
            $existing_provider = csso_search_in_array( $this->target_env_config, 'provider', $provider['provider'] );
            
            if ( $existing_provider ) {
                continue;
            } else {
                unset( $provider['id'] );
            }
            
            $provider['env_id'] = $this->target_env_id;
            $this->providers_manager->csso_create_or_replace( $provider );
        }
    }
    
    private function csso_migrate_only_mapping()
    {
        foreach ( $this->source_env_config as $provider ) {
            $existing_provider = csso_search_in_array( $this->target_env_config, 'provider', $provider['provider'] );
            
            if ( $existing_provider ) {
                $existing_provider['attribute_mapping'] = $provider['attribute_mapping'];
                $existing_provider['custom_attributes'] = $provider['custom_attributes'];
                $existing_provider['role_mapping'] = $provider['role_mapping'];
                $this->providers_manager->csso_create_or_replace( $existing_provider );
            }
        
        }
    }

}