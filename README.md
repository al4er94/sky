# sky
skynet test

Тестовое задание https://sknt.ru/job/backend/
Api.php - абстрактный класс, от которого будет наследоваться рабочий класс 
userApi.php - рабочий класс для данного api, который реализует абстрактные методы родительского класса (viewAction и updateAction)
db.php - класс для работы с БД. Реализует статические методы взаимодействия Api с БД
.htaccess - перенаправляет все запросы на точку входа - index.php
В файле index.php созда экземпляр класса userApi