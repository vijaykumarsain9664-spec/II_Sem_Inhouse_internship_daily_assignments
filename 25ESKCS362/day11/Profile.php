<?php
include "header.php";
if (!isset($_SESSION["uid"])) {
    header("Location: login.php");
    exit;
}
?>
<h2>My Profile</h2>
<img id="avatarPreview" class="avatar-large" src="uploads/<?= htmlspecialchars($_SESSION['pic']) ?>">
<p><strong>Name:</strong> <?= htmlspecialchars($_SESSION["name"]) ?></p>
 
<h3>Update Profile Picture</h3>
<form id="picForm" enctype="multipart/form-data">
    <input type="file" name="picture" id="pictureInput" accept="image/*" required>
    <button type="submit">Upload</button>
</form>
<p id="uploadMsg"></p>
 
<script>
$(function () {
    $("#picForm").on("submit", function (e) {
        e.preventDefault();
        var formData = new FormData(this);
 
        $.ajax({
            url: "upload_picture.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                var data = JSON.parse(response);
                if (data.success) {
                    $("#avatarPreview").attr("src", "uploads/" + data.filename);
                    $("#uploadMsg").text("Picture updated!");
                } else {
                    $("#uploadMsg").text(data.error);
                }
            }
        });
    });
});
</script>
<?php include "footer.php"; ?>
 