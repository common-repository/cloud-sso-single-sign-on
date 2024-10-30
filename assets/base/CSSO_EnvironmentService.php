<?php

namespace CloudSingleSignOn\base;

class CSSO_EnvironmentService
{
    private  $table_name ;
    private  $envs ;
    public function csso_register()
    {
        $this->csso_set_table_name();
        $this->envs = $this->csso_get_all_envs();
        $this->csso_set_current_environment( $this->csso_get_env_by_name( 'default' ) );
    }
    
    private function csso_set_table_name()
    {
        global  $wpdb ;
        $this->table_name = $wpdb->prefix . csso_get_plugin_prefix() . 'environments';
    }
    
    public function csso_get_all_envs()
    {
        global  $wpdb ;
        return $wpdb->get_results( "SELECT * FROM {$this->table_name}", ARRAY_A );
    }
    
    private function csso_is_environment_current( $url ) : bool
    {
        return !(strpos( get_site_url(), $url ) === false);
    }
    
    public function csso_get_env_by_name( $env_name )
    {
        return csso_search_in_array( $this->envs, 'name', $env_name );
    }
    
    private function csso_set_current_environment( $env )
    {
        update_option( csso_get_plugin_prefix() . 'current_environment', json_encode( $env ) );
    }
    
    public function csso_get_env_by_url( $env_url )
    {
        return csso_search_in_array( $this->envs, 'url', $env_url );
    }
    
    public function csso_get_current_environment()
    {
        return json_decode( get_option( csso_get_plugin_prefix() . 'current_environment' ), true );
    }
    
    private function csso_set_editable_environment( $env_name )
    {
        update_option( csso_get_plugin_prefix() . 'active_editable_environment', $env_name );
    }

}