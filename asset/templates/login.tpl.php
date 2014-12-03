<div id="content_holder">
	<div id="main">
		<div id="content">
		
			<div class="login">
				<p class="header"><span id="logo"></span>Asset Tracking</p>

				<form name="frmLogin" id="frmLogin" method="post">
					<div class="fieldgroup">
						<label for="txtUsername">
							<?= ($_CONTROL->error != 'failed')?'Username':'<span class="error">Username</span>'; ?>
						</label>
						<div class="field">
							<input type="text" size="30" maxlength="100" name="txtUsername" id="txtUsername" />
						</div>
					</div>
					<div class="fieldgroup">
						<label for="txtPassword">
							<?= ($_CONTROL->error != 'failed')?'Password':'<span class="error">Password</span>'; ?>
						</label>
						<div class="field">
							<input type="password" size="30" maxlength="50" name="txtPassword" id="txtPassword" />
						</div>
					</div>

				<div class="login-controls">
					<input type="submit" value="Log In" id="btnLogIn" class="button" />
					<input type="hidden" name="pg" value="1" />
				</div>
				<div class="cf"></div>
			</form>
		</div>

	</div>
</div>

<div id="debug">
	<p>
		<?php printf('<pre>%s</pre>', print_r($_REQUEST, true)); ?>
	</p>
	<p>
		<?php printf('<pre>%s</pre>', print_r($_SESSION, true)); ?>
	</p>
</div>
