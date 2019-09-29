<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <?php if ($isCategory && $isProducts) : ?>
                    <div class="col-12 text-center">
                        <h3>Справочник медицинских препаратов</h3>
                    </div>
                <?php endif; ?>
                <?php if (!$isCategory) : ?>
                    <div class="col-6 text-center">
                        <div class="file-upload">
                            <input type="file" name="file-category" id="file-category" class="input-file" onchange="getFileName(this.id);">
                            <label for="file-category" class="btn btn-outline-dark">
                                <span>Загрузить файл с категориями</span>
                            </label>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (!$isProducts) : ?>
                    <div class="col-6 text-center">
                        <div class="file-upload">
                            <input type="file" name="file-products" id="file-products" class="input-file" onchange="getFileName(this.id);">
                            <label for="file-products" class="btn btn-outline-dark">
                                <span>Загрузить файл с продуктами</span>
                            </label>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <?php if (isset($catalog)) echo build_tree($catalog); ?>
            <?php if (!isset($catalog)) : ?>
                <div class="alert alert-danger text-center" role="alert">
                    Не загружены файлы с категориями или продуктами
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<script>
    function getFileName(id) {
        let formData = new FormData();
        let fileField = document.getElementById(id);

        formData.append(id, fileField.files[0]);

        fetch('/upload.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .catch(error => console.error('Ошибка:', error))
            .then(response => window.location.reload());
    }
</script>
</body>
</html>