<?php

namespace App\Service;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class CrudTwigServicer 
{
    private $dir;
    private $data;
    
    public function index(): static
    {
        $this->createFolder();
        $this->createIndex();
        $this->createNew();
        $this->createEdit();
        $this->createForm();
        return $this;
    }

    private function createFolder() : static
    {
        $filesystem = new Filesystem();
        try {
            if (!file_exists($this->getDir().'/../templates/'.$this->data['crud']['route'])) {
                mkdir($this->getDir().'/../templates/'.$this->data['crud']['route']);
            }
        } catch (IOExceptionInterface $exception) {
            return "An error occurred while creating your directory at ".$exception->getPath();
        }
        return $this;
    }

    private function createIndex() : static
    {
        $arrayTable = [];
        foreach ($this->data['fields'] as $key => $value) {
            if ($value['datatable'] == 1) {
                array_push($arrayTable, $value['name']);
            }
        }
        // calculate width with count fields 
        $widthField = 85 / count($arrayTable);
        // store to div
        $divFields = "";
        foreach ($arrayTable as $key => $value) {
            $divFields .= "\n\t\t\t\t\t\t\t<th style=\"width: ".$widthField."\">".$value."</th>";
        }
        $string = "{% extends 'adminlte.html.twig' %}

{% block title %}".$this->data['crud']['entity']."{% endblock %}

{% block stylesheets %}
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap.css\" integrity=\"sha512-GgMRV6UXqOFCfW5B+j4bvTRCU9vGYA9o46NiaMUrlHGcSJHg68o7X7IGAPl+pvaApCjCU3C5MsWhgDGGkmaaPg==\" crossorigin=\"anonymous\" referrerpolicy=\"no-referrer\" />
    
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap4.css\" integrity=\"sha512-dufHiZudcIxbGBWe8xgxnZZyrDstYWv60bXzUww0cl9az56Y86/qEzGDW3lYkQxCFmr/H79i2Kl+uip/KEmw7Q==\" crossorigin=\"anonymous\" referrerpolicy=\"no-referrer\" />
{% endblock %}

{% block body %}
<div class=\"content-wrapper\">

    <section class=\"content-header\">
        <div class=\"container-fluid\">
            <div class=\"row mb-2\">
                <div class=\"col-sm-6\">
                    <h1>".$this->data['crud']['entity']."</h1>
                </div>
                <div class=\"col-sm-6\">
                    <ol class=\"breadcrumb float-sm-right\">
                        <li class=\"breadcrumb-item\"><a href=\"#\">Home</a></li>
                        <li class=\"breadcrumb-item active\">".$this->data['crud']['entity']."</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class=\"content\">
        {% for message in app.flashes('success') %}
            <div class=\"alert alert-success\">
                {{ message }}
            </div>
        {% endfor %}
        <div class=\"card\">
            <div class=\"card-header\">
                <h3 class=\"card-title\">Data ".$this->data['crud']['entity']."</h3>
                <div class=\"card-tools\">
                    <a href=\"{{ path('app_".$this->data['crud']['route']."_new')}}\" class=\"btn btn-tool\">
                        <i class=\"fas fa-plus\"></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-tool\" data-card-widget=\"remove\" title=\"Remove\">
                        <i class=\"fas fa-times\"></i>
                    </button>
                </div>
            </div>
            <div class=\"card-body\">
                <table class=\"table table-bordered table-hover\" id=\"example\">
                    <thead>
                        <tr>
                            <th style=\"width: 5%;\">No</th>
                            <th style=\"width: 10%;\">actions</th>".$divFields."
                        </tr>
                    </thead>
                    <tbody>
                    
                    </tbody>
                </table>
            </div>

        </div>

    </section>

</div>

{% endblock %}

{% block javascripts %}
    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js\" integrity=\"sha512-BkpSL20WETFylMrcirBahHfSnY++H2O1W+UnEEO4yNIl+jI2+zowyoGJpbtk6bx97fBXf++WJHSSK2MV4ghPcg==\" crossorigin=\"anonymous\" referrerpolicy=\"no-referrer\"></script>
    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap4.min.js\" integrity=\"sha512-OQlawZneA7zzfI6B1n1tjUuo3C5mtYuAWpQdg+iI9mkDoo7iFzTqnQHf+K5ThOWNJ9AbXL4+ZDwH7ykySPQc+A==\" crossorigin=\"anonymous\" referrerpolicy=\"no-referrer\"></script>

    <script>
        $(document).ready(function () {
            table = $('#example').DataTable({
                scrollCollapse: true,
                responsive: true,
                autoWidth: false,
                language: { search: \"\",
                    searchPlaceholder: \"Search\",
                    sLengthMenu: \"_MENU_items\"
                },
                \"order\": [],
                \"ajax\": {
                    \"url\": \" {{ path('app_".$this->data['crud']['route']."_ajax') }}\",
                    \"headers\": {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    \"type\": \"POST\",
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR.responseText);
                    }
                },
                //optional
                \"columnDefs\": [{
                    \"targets\": [0, 1],
                    \"orderable\": false,
                }, ],
            });
        });
        
        function reload_table() {
            table.ajax.reload(null, false);
        }
    </script>
{% endblock %}
        ";
        $path = $this->getDir().'/../templates/'.$this->data['crud']['route'].'/index.html.twig';
        $create = fopen($path, "w") or die("Change your permision folder for application and harviacode folder to 777");
        fwrite($create, $string);
        fclose($create);
        return $this;
    }

    private function createNew() : static
    {
        $string = $this->makeForm('Save', 'Tambah');
        $path = $this->getDir().'/../templates/'.$this->data['crud']['route'].'/new.html.twig';
        $create = fopen($path, "w") or die("Change your permision folder for application and harviacode folder to 777");
        fwrite($create, $string);
        fclose($create);
        return $this;
    }

    private function createEdit() : static
    {
        $string = $this->makeForm('Update', 'Update');
        $path = $this->getDir().'/../templates/'.$this->data['crud']['route'].'/edit.html.twig';
        $create = fopen($path, "w") or die("Change your permision folder for application and harviacode folder to 777");
        fwrite($create, $string);
        fclose($create);
        return $this;
    }

    private function makeForm($action, $label) 
    {
        $string = "{% extends 'adminlte.html.twig' %}

{% block title %}".$this->data['crud']['entity']."{% endblock %}

{% block stylesheets %}
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap.css\" integrity=\"sha512-GgMRV6UXqOFCfW5B+j4bvTRCU9vGYA9o46NiaMUrlHGcSJHg68o7X7IGAPl+pvaApCjCU3C5MsWhgDGGkmaaPg==\" crossorigin=\"anonymous\" referrerpolicy=\"no-referrer\" />
    
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap4.css\" integrity=\"sha512-dufHiZudcIxbGBWe8xgxnZZyrDstYWv60bXzUww0cl9az56Y86/qEzGDW3lYkQxCFmr/H79i2Kl+uip/KEmw7Q==\" crossorigin=\"anonymous\" referrerpolicy=\"no-referrer\" />
{% endblock %}

{% block body %}
<div class=\"content-wrapper\">

    <section class=\"content-header\">
        <div class=\"container-fluid\">
            <div class=\"row mb-2\">
                <div class=\"col-sm-6\">
                    <h1>".$this->data['crud']['entity']."</h1>
                </div>
                <div class=\"col-sm-6\">
                    <ol class=\"breadcrumb float-sm-right\">
                        <li class=\"breadcrumb-item\"><a href=\"#\">Home</a></li>
                        <li class=\"breadcrumb-item\">".$this->data['crud']['entity']."</li>
                        <li class=\"breadcrumb-item active\">".$label."</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class=\"content\">
        {% for message in app.flashes('success') %}
            <div class=\"alert alert-success\">
                {{ message }}
            </div>
        {% endfor %}
        <div class=\"card\">
            <div class=\"card-header\">
                <h3 class=\"card-title\">Data ".$this->data['crud']['entity']."</h3>
                <div class=\"card-tools\">
                    <a href=\"{{ path('app_".$this->data['crud']['route']."')}}\" class=\"btn btn-tool\">
                        <i class=\"fas fa-list\"></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-tool\" data-card-widget=\"remove\" title=\"Remove\">
                        <i class=\"fas fa-times\"></i>
                    </button>
                </div>
            </div>
            <div class=\"card-body\">
                {{ include('".$this->data['crud']['route']."/_form.html.twig', {'button_label': '".$action."'}) }}
            </div>

        </div>

    </section>

</div>

{% endblock %}

{% block javascripts %}
    <script>
        
    </script>
{% endblock %}
        ";
        return $string;
    }

    private function createForm() : static
    {
        $string = "{{ form_start(form) }}
    {{ form_widget(form) }}
    <button class=\"btn btn-primary\">{{ button_label|default('Save') }}</button>
{{ form_end(form) }}";
        $path = $this->getDir().'/../templates/'.$this->data['crud']['route'].'/_form.html.twig';
        $create = fopen($path, "w") or die("Change your permision folder for application and harviacode folder to 777");
        fwrite($create, $string);
        fclose($create);
        return $this;
    }

    /**
     * Get the value of dir
     */ 
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * Set the value of dir
     *
     * @return  self
     */ 
    public function setDir($dir)
    {
        $this->dir = $dir;

        return $this;
    }

    /**
     * Get the value of data
     */ 
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of data
     *
     * @return  self
     */ 
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}