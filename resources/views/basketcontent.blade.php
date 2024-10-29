<div id="basket-content">
<h1>Basket</h1>
<div class="form-container">
<span><b>Use of Postman API in PHP/Laravel</b></span>
<br/><br/>
<b>get products:</b> GET request to list all products from database<br/>
<b>get csrf:</b> GET request to save in collection variables the form token, needed for POST requests<br/>
<b>get basket:</b> GET request to retrieve current items in the basket and user wallet<br/>
<b>update basket:</b> POST request to set products in the basket, real quantity is invisible on this step, checks wallet total, deletes previous basket<br/>
<b>purchase basket:</b> POST request to sum transaction query and set the user purchase from the basket, checks all baskets to ensure 1 quantity left per user and manages maximum per product using stored total (per user max is 5), clears the basket<br/>
</div>
<br/>
<div class="back-main">
<a href="/">Back to main</a>
</div>
</div>