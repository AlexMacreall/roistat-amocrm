
<script type="text/javascript">
    window.onload = function() {
        var startTime = new Date().getTime();
        document.getElementById("start_time").setAttribute("value", startTime);
    };
    function calculateTime() {

        event.preventDefault();

        var startTime = parseInt(document.getElementById("start_time").value, 10); 
        var endTime = new Date().getTime(); 
        var timeSpent = Math.floor((endTime - startTime) / 1000);
        var isTimeOver30s = 0;
        if (timeSpent >= 30)
            isTimeOver30s = 1;
        document.getElementById("time_spent").setAttribute("value", isTimeOver30s);

        document.querySelector("form").submit();

}
</script>

<input type="text" style="display: none" id="start_time" />

<form action="api/lead-create.php" method="get" onsubmit="calculateTime();">
    <label for="name">Name имя</label><br />
    <input type="text" id="client_name" name="client_name" required/><br />

    <label for="email">E-mail</label><br />
    <input type="email" id="client_email" name="client_email" required/><br />

    <label for="tel">Phone number</label><br />
    <input type="tel" id="client_phone" name="client_phone" required/><br />

    <label for="price">Price</label><br />
    <input type="number" id="product_price" name="product_price" required/><br />
    <input type="number" id="time_spent" name="time_spent" style="display: none" />
    <input type="submit" value="Sumbit" />

</form>

