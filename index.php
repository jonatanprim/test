<?php
require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=tugasdicoding;AccountKey=tmWNM9zdwFq+NG/Tait0sv5fCX2VTJ/gCy1JJ7v97IWP5Yk2foOGo4KkjMB8bnYXcWChmlptYuU9ODtA9iL0AQ==;";
$containerName = "jonatanprim";
// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);
if (isset($_POST['submit'])) {
	$fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
	$content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
	// echo fread($content, filesize($fileToUpload));
	$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
	header("Location: index.php");
}
$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
?>

<!DOCTYPE html>
<html>
 <head>
 <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Submission 2</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/starter-template/">

    <!-- Bootstrap core CSS -->
    <link href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="starter-template.css" rel="stylesheet">
  </head>
<body>
	<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarsExampleDefault">
			<h1>Azure Storage dan Azure Cognitive</h1>
		</div>
		</nav>
		<main role="main" class="container">
    		<div class="starter-template"> <br><br><br>
        		
        		<h3>Uploud gambar ke Azure Blob Storage</h3>
				<p class="lead">Pilih foto yang ingin anda uploud <br> kemudian click <b> Upload </b>, setelah foto tersimpan, maka nama file dan file URL akan ditampilkan dalam tabel</p>
				<span class="border-top my-3"></span>
			</div>
		<div class="mt-4 mb-2">
			<form class="d-flex justify-content-lefr" action="index.php" method="post" enctype="multipart/form-data">
				<input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required="">
				<input type="submit" name="submit" value="Upload">
			</form>
		</div>
		<br>
		<br>
		<table class='table table-hover'>
			<thead>
				<tr>
					<th>Nama File</th>
					<th>File URL</th>
				</tr>
			</thead>
			<tbody>
				<?php
				do {
					foreach ($result->getBlobs() as $blob)
					{
						?>
						<tr>
							<td><?php echo $blob->getName() ?></td>
							<td><?php echo $blob->getUrl() ?></td>
						</tr>
						<?php
					}
					$listBlobsOptions->setContinuationToken($result->getContinuationToken());

				} while($result->getContinuationToken());
				?>
			</tbody>
		</table>
	</div>


	<script type="text/javascript">
	    function processImage() {
	        // **********************************************
	        // *** Update or verify the following values. ***
	        // **********************************************
	 
	        // Replace <Subscription Key> with your valid subscription key.
	        var subscriptionKey = "e0d4854bbec94fa9850f52ef0a5f2b30";
	 
	        // You must use the same Azure region in your REST API method as you used to
	        // get your subscription keys. For example, if you got your subscription keys
	        // from the West US region, replace "westcentralus" in the URL
	        // below with "westus".
	        //
	        // Free trial subscription keys are generated in the "westus" region.
	        // If you use a free trial subscription key, you shouldn't need to change
	        // this region.
	        var uriBase =
	            "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";
	 
	        // Request parameters.
	        var params = {
	            "visualFeatures": "Categories,Description,Color",
	            "details": "",
	            "language": "en",
	        };
	 
	        // Display the image.
	        var sourceImageUrl = document.getElementById("inputImage").value;
	        document.querySelector("#sourceImage").src = sourceImageUrl;
	 
	        // Make the REST API call.
	        $.ajax({
	            url: uriBase + "?" + $.param(params),
	 
	            // Request headers.
	            beforeSend: function(xhrObj){
	                xhrObj.setRequestHeader("Content-Type","application/json");
	                xhrObj.setRequestHeader(
	                    "Ocp-Apim-Subscription-Key", subscriptionKey);
	            },
	 
	            type: "POST",
	 
	            // Request body.
	            data: '{"url": ' + '"' + sourceImageUrl + '"}',
	        })
	 
	        .done(function(data) {
	            // Show formatted JSON on webpage.
	            $("#responseTextArea").val(JSON.stringify(data, null, 2));
	            // console.log(data);
                // var json = $.parseJSON(data);
                $("#description").text(data.description.captions[0].text);
	        })
	 
	        .fail(function(jqXHR, textStatus, errorThrown) {
	            // Display error message.
	            var errorString = (errorThrown === "") ? "Error. " :
	                errorThrown + " (" + jqXHR.status + "): ";
	            errorString += (jqXHR.responseText === "") ? "" :
	                jQuery.parseJSON(jqXHR.responseText).message;
	            alert(errorString);
	        });
	    };
	</script>
	 
	<h3>Analisis gambar dengan Cognitive Services</h3>
	Copy File URL dari foto yang telah anda uploud ke kolom yang telah disediakan, kemudian klick <strong>Analyze image</strong> deskripsi akan ditampilkan dibawah gambar
	<br><br>
	Image to analyze:
	<input type="text" name="inputImage" id="inputImage"
	    value="" />
	<button onclick="processImage()">Analyze image</button>
	<br><br>
	<div id="wrapper" style="width:1020px; display:table;">
	    <div id="jsonOutput" style="width:600px; display:table-cell;">
	        Response:
	        <br><br>
	        <textarea id="responseTextArea" class="UIInput"
	                  style="width:580px; height:400px;"></textarea>
	    </div>
	    <div id="imageDiv" style="width:420px; display:table-cell;">
	        Source image:
	        <br><br>
	        <img id="sourceImage" width="400" height="400" />
	        <br>
			<h3 id="description">description</h3>
	    </div>
	</div>
</html>