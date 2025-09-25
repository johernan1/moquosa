<?php
if (!trait_exists('myio')) {
    trait myio {
        // Lista de tokens que se leen
        // Si un token no existe se sustituye por su nombre
        // TODO: cambiar nombre a este fichero

        // Definición de propiedades de la clase
        public $tokens_frame_connectivity;
        public $n_tokens_frame_connectivity;
        public $tokens_frame_connectivity_normalized;

        public $tokens_joint_coordinates;
        public $n_tokens_joint_coordinates;
        public $tokens_joint_coordinates_normalized;

        public $tokens_section_properties;
        public $n_tokens_section_properties;
        public $tokens_section_properties_normalized;

        public $tokens_frame_section_assignments;
        public $n_tokens_frame_section_assignments;

        public $tokens_restraint_assignments;
        public $n_tokens_restraint_assignments;
        public $tokens_restraint_assignments_normalized;

        public $tokens_DOF;
        public $n_tokens_DOF;

        public $tokens_joint_loads_force;
        public $n_tokens_joint_loads_force;
        public $tokens_joint_loads_force_normalized;

        public $tokens_frame_loads_distributed;
        public $n_tokens_frame_loads_distributed;
        public $tokens_frame_loads_distributed_normalized;

        public $tokens_frame_loads_point;
        public $n_tokens_frame_loads_point;
        public $tokens_frame_loads_point_normalized;

    	public $tokens_frame_moments_point;
        public $n_tokens_frame_moments_point;
        public $tokens_frame_moments_point_normalized;

        // Constructor para inicializar las propiedades
        public function miconstructor() {
            // print_object("^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^\n");
            // print_object("^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^\n");
            // print_object("^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^\n");
            // print_object("^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^\n");
            //----------------------------------------------------------------
            $this->tokens_frame_connectivity = ["Frame", "JointI", "JointJ", "GUID"];
            $this->n_tokens_frame_connectivity = count($this->tokens_frame_connectivity);
            // Se añaden columnas en el modelo normalizado
            $this->tokens_frame_connectivity_normalized = $this->tokens_frame_connectivity;

            //----------------------------------------------------------------
            $this->tokens_joint_coordinates = ["Joint", "XorR", "Y", "Z", "GUID"];
            $this->n_tokens_joint_coordinates = count($this->tokens_joint_coordinates);
            // Se añaden columnas en el modelo normalizado
            $this->tokens_joint_coordinates_normalized = $this->tokens_joint_coordinates;
            //print_object($this->tokens_joint_coordinates);

            //----------------------------------------------------------------
            $this->tokens_section_properties = ["SectionName", "Material", "Area", "I33", "AMod", "I3Mod", "Notes"];
            $this->n_tokens_section_properties = count($this->tokens_section_properties);
            // Se añaden columnas en el modelo normalizado
            $this->tokens_section_properties_normalized = $this->tokens_section_properties;

            //----------------------------------------------------------------
            $this->tokens_frame_section_assignments = ["Frame", "AnalSect"];
            $this->n_tokens_frame_section_assignments = count($this->tokens_frame_section_assignments);

            //----------------------------------------------------------------
            $this->tokens_restraint_assignments = ["Joint", "U1", "U2", "U3", "R1", "R2", "R3"];
            $this->n_tokens_restraint_assignments = count($this->tokens_restraint_assignments);
            // Se añaden columnas en el modelo normalizado
            $this->tokens_restraint_assignments_normalized = $this->tokens_restraint_assignments;

            //----------------------------------------------------------------
            $this->tokens_DOF = ["UX", "UY", "UZ", "RX", "RY", "RZ"];
            $this->n_tokens_DOF = count($this->tokens_DOF);

            //----------------------------------------------------------------
            $this->tokens_joint_loads_force = ["Joint", "LoadPat", "CoordSys", "F1", "F2", "F3", "M1", "M2", "M3", "GUID"];
            $this->n_tokens_joint_loads_force = count($this->tokens_joint_loads_force);
            // Se añaden columnas en el modelo normalizado
            $this->tokens_joint_loads_force_normalized = $this->tokens_joint_loads_force;

            //----------------------------------------------------------------
            $this->tokens_frame_loads_distributed = ["Frame", "LoadPat", "CoordSys", "Type", "Dir", "RelDistA", "RelDistB", "FOverLA", "FOverLB", "GUID"];
            $this->n_tokens_frame_loads_distributed = count($this->tokens_frame_loads_distributed);
            // Se añaden columnas en el modelo normalizado
            $this->tokens_frame_loads_distributed_normalized = $this->tokens_frame_loads_distributed;

            //----------------------------------------------------------------
            $this->tokens_frame_loads_point = ["Frame", "LoadPat", "CoordSys", "Type", "Dir", "RelDist", "Force", "GUID"];
            $this->n_tokens_frame_loads_point = count($this->tokens_frame_loads_point);
            // Se añaden columnas en el modelo normalizado
            $this->tokens_frame_loads_point_normalized = $this->tokens_frame_loads_point;

            //----------------------------------------------------------------
            $this->tokens_frame_moments_point = ["Frame", "LoadPat", "CoordSys", "Type", "Dir", "RelDist", "Moment", "GUID"];
            $this->n_tokens_frame_moments_point = count($this->tokens_frame_moments_point);
            // Se añaden columnas en el modelo normalizado
            $this->tokens_frame_moments_point_normalized = $this->tokens_frame_moments_point;
        }
    
    }
}
?>
