<div class="col-md-4">
          <sidebar>
            <div class="card bg-light">
              <div class="card-body">
                <h4>Blog search</h4>

                <form class="row" action="/final" method="GET">
                  <div class="col-auto">
                    <input type="text" class="form-control" id="search" name="search"> 
                  </div>
                  <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Buscar</button>                  </div>
                </form>
              </div>
            </div>

            <div class="card bg-light">
              <div class="card-body">
                <h4>Sign In</h4>
                <form>
                  <div class="mb-3">
                    <input type="text" class="form-control" id="username" placeholder="Ingrese su usuario">
                  </div>
                  <div class="mb-3">
                    <input type="password" class="form-control" id="password" placeholder="Ingrese su contraseña">
                  </div>
                  <button type="submit" class="btn btn-primary">Ingresar</button>
                </form>
              </div>
            </div>

            <div class="card bg-light">
              <div class="card-body">
                <h4>Categorías</h4>
                <ul class="list-unstyled">
                  <?php foreach(getCategories() as $category) { ?>
                    <li><a href="#<?php echo $category['cat_id']; ?>"><?php echo $category['cat_title']; ?></a></li>
                  <?php } ?>
                  <li><a href="#">Javascript</a></li>
                </ul>
              </div>
            </div>
          </sidebar>
        </div>