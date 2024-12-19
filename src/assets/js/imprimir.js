async function imprimirTicket(datos,form,cliente,direccion,telefono,pago,ticket,reciboneto,reciboigv,recibototal){
    const fecha = new Date();
    let imp = localStorage.getItem("impresora");
  if(imp==""){
    console.log("impresora vacia")
    this.snackBar.open("Impresora no configurada", undefined, { duration: 8000, verticalPosition: 'bottom', panelClass: ['snackbar-error'] });
  }

    let nombreImpresora = imp;
              let api_key = "123456"
              const conector = new connetor_plugin()
                          conector.textaling("center")
                         conector.img_url("https://lh-cjm.com/erp/assets/img/logo-erp-b-n.jpg");
                         conector.feed("1")
                         conector.fontsize("2")
                         conector.text(localStorage.getItem("sucursal"))
                          conector.fontsize("1")
                          conector.text("Ferreteria y materiales de contruccion")
                          if(localStorage.getItem("email")!='-'){
                          conector.text(localStorage.getItem("email"))
                          }
                          conector.text(localStorage.getItem("direccion"))
                          conector.text("Telefonos: "+localStorage.getItem("telefono"))
                          conector.text("Lima - Lima")
                          conector.text("RUC: 2053799520")
                          conector.feed("1")
                          conector.textaling("left")
                          conector.text("ADQUIRIENTE:")
                          conector.text(cliente)
                          if(direccion!=''){
                          conector.text("Direccion:"+direccion)
                          }
                          if(telefono!=''){
                            conector.text("Telefono:"+telefono)
                          }
                          conector.textaling("left")
                          conector.text("Fecha:"+fecha.toLocaleDateString() +" "+ fecha.getHours()+":"+fecha .getMinutes()+":"+fecha.getSeconds())
                          conector.text("Numero de ticket:"+ticket)
                          conector.feed("1")
                          conector.textaling("center")
                          conector.text("Descripcion      Cant.     Precio     Importe")
                          conector.text("===============================================")
                          for (let index = 0; index < datos.length; index++) {
                            const element = datos[index];
                            var precio =element.cantidad * element.precio -(element.descuento*element.cantidad)
                            var subtotal = element.cantidad * element.precio-(element.descuento*element.cantidad);
                            conector.textaling("left");
                            conector.text(index+1+") "+element.nombre);
                            conector.textaling("right");
                            conector.text("           "+element.cantidad+"        S/ "+(precio/element.cantidad).toFixed(2)+"        S/ "+subtotal.toFixed(2))
                            conector.textaling("center")
                            conector.text("---------------------------------------------")
                          }
                          conector.feed("1")
                          conector.textaling("center")
                          conector.text("==============================================")
                          conector.fontsize("1")
                          conector.textaling("right")
                          conector.text("Op. Gravadas: S/ "+reciboneto)
                          if(form.value.igv>0){
                          conector.text("I.G.V: S/ "+reciboigv)
                          }
                          conector.text("Total: S/ "+recibototal)

                           conector.feed("1")
                          conector.textaling("center")
                          conector.text("**********************************")
                          conector.text("SON:"+pago)

                          const resp = await conector.imprimir(nombreImpresora, api_key);
                          if (resp === true) {

                               console.log("imprimio: "+resp)
                          } else {
                               console.log("Problema al imprimir: "+resp)

                          }

  }