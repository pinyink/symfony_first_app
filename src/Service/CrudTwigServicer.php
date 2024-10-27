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
        $this->createShow();
        $this->createDelete();
        return $this;
    }

    private function createFolder() : static
    {
        $filesystem = new Filesystem();
        try {
            if (!file_exists($this->getDir().'/../templates/'.strtolower($this->data['crud']['route']))) {
                mkdir($this->getDir().'/../templates/'.strtolower($this->data['crud']['route']));
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
                array_push($arrayTable, $value['label']);
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
                    <a href=\"{{ path('app_".strtolower($this->data['crud']['route'])."_new')}}\" class=\"btn btn-tool\">
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
                    \"url\": \" {{ path('app_".strtolower($this->data['crud']['route'])."_ajax') }}\",
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
        $path = $this->getDir().'/../templates/'.strtolower($this->data['crud']['route']).'/index.html.twig';
        $create = fopen($path, "w") or die("Change your permision folder for application and harviacode folder to 777");
        fwrite($create, $string);
        fclose($create);
        return $this;
    }

    private function createNew() : static
    {
        $string = $this->makeForm('Save', 'Tambah');
        $path = $this->getDir().'/../templates/'.strtolower($this->data['crud']['route']).'/new.html.twig';
        $create = fopen($path, "w") or die("Change your permision folder for application and harviacode folder to 777");
        fwrite($create, $string);
        fclose($create);
        return $this;
    }

    private function createEdit() : static
    {
        $string = $this->makeForm('Update', 'Update');
        $path = $this->getDir().'/../templates/'.strtolower($this->data['crud']['route']).'/edit.html.twig';
        $create = fopen($path, "w") or die("Change your permision folder for application and harviacode folder to 777");
        fwrite($create, $string);
        fclose($create);
        return $this;
    }

    private function makeForm($action, $label) 
    {
        $javascript = "$(document).ready(function() {";
        foreach ($this->data['fields'] as $key => $value) {
            if ($value['type'] == 3) {
                $javascript .= "
            $(\"#".strtolower($this->data['crud']['entity'])."_".$value['name']."\").change(function(e) {
                file_select(e, '".$value['name']."');
            });";
            }
        }
        $javascript .= "\n\t\t});";
        $string = "{% extends 'adminlte.html.twig' %}

{% block title %}".$this->data['crud']['entity']."{% endblock %}

{% block stylesheets %}
    
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
                    <a href=\"{{ path('app_".strtolower($this->data['crud']['route'])."')}}\" class=\"btn btn-tool\">
                        <i class=\"fas fa-list\"></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-tool\" data-card-widget=\"remove\" title=\"Remove\">
                        <i class=\"fas fa-times\"></i>
                    </button>
                </div>
            </div>
            <div class=\"card-body\">
                {{ include('".strtolower($this->data['crud']['route'])."/_form.html.twig', {'button_label': '".$action."'}) }}
            </div>

        </div>

    </section>

</div>

{% endblock %}

{% block javascripts %}
    <script>
        ".$javascript."
    </script>
{% endblock %}
        ";
        return $string;
    }
    
    private function createForm() : static
    {
        $formRow = "";
        foreach ($this->data['fields'] as $key => $value) {
            if ($value['type'] == 3) {
                $formRow .= "\n\t{{ form_row(form.".$value['name'].") }}";
                $formRow .= "\n\t<div class=\"row mb-2\">
                <div class=\"col-md-2\" id=\"divimage_".$value['name']."\">
                    <img src=\"{{ asset('img/image.jpg') }}\" alt=\"\" class=\"img img-thumbnail img-preview \" id=\"img-preview-".$value['name']."\" style=\"width: 100px; height: 100px;\">
                </div>
                {% if (button_label == 'Update') %}
                    <div class=\"col-md-2\" id=\"divcol_".$value['name']."\">
                        <img src=\"{{ asset(image_directory ~ '".strtolower($this->data['crud']['entity'])."/' ~ ".strtolower($this->data['crud']['entity']).".".$value['name'].") }}\" alt=\"\" class=\"img img-thumbnail img-preview\" id=\"img-old-".$value['name']."\" style=\"width: 100px; height: 100px;\">
                    </div>
                {% endif %}
            </div>";
            } else {
                $formRow .= "\n\t{{ form_row(form.".$value['name'].") }}";
            }
        }
        $string = "{{ form_start(form) }}".$formRow."
    <button class=\"btn btn-primary\"><i class=\"fas fa-edit\"></i> {{ button_label|default('Save') }}</button>
{{ form_end(form) }}";
        $path = $this->getDir().'/../templates/'.strtolower($this->data['crud']['route']).'/_form.html.twig';
        $create = fopen($path, "w") or die("Change your permision folder for application and harviacode folder to 777");
        fwrite($create, $string);
        fclose($create);
        return $this;
    }

    private function createShow() : static
    {
        $table = "<table class=\"table\">";
        foreach ($this->data['fields'] as $key => $value) {
            if ($value['type'] == 3) {
                $table .= "\n\t\t\t\t\t<tr>
                        <td style=\"width: 20%\">".$value['label']."</td>
                        <td style=\"width: 5%;\">:</td>
                        <td style=\"width: 75%;\">{{ image_directory ~ '".strtolower($this->data['crud']['entity'])."/' ~ ".strtolower($this->data['crud']['entity']).".".$value['name']." }} <br><img src=\"{{ asset(image_directory ~ '".strtolower($this->data['crud']['entity'])."/' ~ ".strtolower($this->data['crud']['entity']).".".$value['name']." ) }}\" alt=\"\" class=\"img img-thumbnail img-preview \" style=\"width: 100px; height: 100px;\"></td>
                    </tr>";
            } if($value['type'] == 4 ) {
                $table .= "\n\t\t\t\t\t<tr>
                        <td style=\"width: 20%\">".$value['label']."</td>
                        <td style=\"width: 5%;\">:</td>
                        <td style=\"width: 75%;\">{{ ".strtolower($this->data['crud']['entity']).".".$value['name']."|number_format(0, ',', '.') }}</td>
                    </tr>";
            } else {
                $table .= "\n\t\t\t\t\t<tr>
                        <td style=\"width: 20%\">".$value['label']."</td>
                        <td style=\"width: 5%;\">:</td>
                        <td style=\"width: 75%;\">{{ ".strtolower($this->data['crud']['entity']).".".$value['name']." }}</td>
                    </tr>";
            }
        }
        $table .= "\n\t\t\t\t</table>";
        $string = "{% extends 'adminlte.html.twig' %}

{% block title %}".$this->data['crud']['entity']."{% endblock %}

{% block stylesheets %}
    
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
                        <li class=\"breadcrumb-item active\">Lihat</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class=\"content\">
        <div class=\"card\">
            <div class=\"card-header\">
                <h3 class=\"card-title\">Data ".$this->data['crud']['entity']."</h3>
                <div class=\"card-tools\">
                    <a href=\"{{ path('app_".strtolower($this->data['crud']['route'])."')}}\" class=\"btn btn-tool\">
                        <i class=\"fas fa-list\"></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-tool\" data-card-widget=\"remove\" title=\"Remove\">
                        <i class=\"fas fa-times\"></i>
                    </button>
                </div>
            </div>
            <div class=\"card-body\">
                ".$table."
            </div>
            <div class=\"card-footer d-flex\" style=\"gap: 5px;\">
                <a href=\"{{ path('app_".strtolower($this->data['crud']['route'])."_edit', { 'id' : ".strtolower($this->data['crud']['entity']).".id}) }}\" class=\"btn btn-info btn-sm pull-right\"><i class=\"fa fa-edit\"></i> edit</a>
                {{ include('".strtolower($this->data['crud']['route'])."/_delete_form.html.twig') }}
            </div>
        </div>

    </section>

</div>

{% endblock %}

{% block javascripts %}
<script>
$('#formDelete').submit(function (e) { 
    e.preventDefault();
    Swal.fire({
        title: \"Are you sure?\",
        text: \"You won't be able to revert this!\",
        icon: \"warning\",
        showCancelButton: true,
        confirmButtonColor: \"#3085d6\",
        cancelButtonColor: \"#d33\",
        confirmButtonText: \"Yes, delete it!\"
        }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
            type: \"POST\",
            url: \"{{ path('app_".strtolower($this->data['crud']['route'])."_delete', {'id': ".strtolower($this->data['crud']['entity']).".id}) }}\",
            data: $('#formDelete').serialize(),
            dataType: \"JSON\",
            success: function (response) {
                Swal.fire({
                    title: \"Notification !\",
                    text: response.message,
                    icon: response.info
                }).then(function (e) {
                    window.location.href = \"{{ path('app_".strtolower($this->data['crud']['route'])."') }}\";
                });;
            }
        });
        }
    });
});
</script>
{% endblock %}
        ";
        $path = $this->getDir().'/../templates/'.strtolower($this->data['crud']['route']).'/show.html.twig';
        $create = fopen($path, "w") or die("Change your permision folder for application and harviacode folder to 777");
        fwrite($create, $string);
        fclose($create);
        return $this;
    }

    private function createDelete() : static 
    {
    $string = "<form method=\"post\" action=\"{{ path('app_".strtolower($this->data['crud']['route'])."_delete', {'id': ".strtolower($this->data['crud']['entity']).".id}) }}\" id=\"formDelete\">
    <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('delete' ~ ".strtolower($this->data['crud']['entity']).".id) }}\">
    <button class=\"btn btn-danger btn-sm\"><i class=\"fa fa-trash\"></i>Delete</button>
</form>
";
        $path = $this->getDir().'/../templates/'.strtolower($this->data['crud']['route']).'/_delete_form.html.twig';
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