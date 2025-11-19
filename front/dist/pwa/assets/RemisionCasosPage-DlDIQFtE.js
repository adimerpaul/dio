import{_ as U,c as u,o as m,w as n,a as i,W as f,$ as k,d as c,Z as v,f as g,a6 as t,h as w,Q,e as C,b,t as q,af as R,a0 as A}from"./index-BP3Sy_Vn.js";import{b as h,c as r}from"./QMenu-DlV7zFtQ.js";import{Q as V}from"./QItemLabel-DN-kSOpd.js";import{Q as I}from"./QList-DjrXdhLu.js";import{Q as z}from"./QBtnDropdown-B5uhW0V6.js";import{Q as D,a as E}from"./QTable-D0FBQRyE.js";import{Q as O}from"./QSpace-C9gxMyir.js";import{Q as _}from"./QSelect-b2PhM9eS.js";import{Q as F}from"./QFile-CvCLLvZC.js";import{Q as j}from"./QForm-CzQbqJ4H.js";import{Q as H}from"./QPage-CaKVPTrr.js";import{C as x}from"./ClosePopup-Rv2ssJwT.js";import"./format-Dgr2a6c2.js";import"./QBtnGroup-TRMGYqby.js";import"./QMarkupTable-d4Zz5VKK.js";import"./use-fullscreen-C5mp_Iol.js";const N={name:"RemisionCasosPage",data(){return{remisiones:[],remision:{},dialogVer:!1,dialog:!1,accion:"",loading:!1,filter:"",archivoFile:null,usuarios:[],dispociciones:["Urgente","Atender Solicitud","Para su conocimiento","Preparar Informe","Hacer seguimiento","Archivo","Invitación"],organizaciones:["Secretario Municipal de Desarrollo Humano","Alcalde Municipal GAMO","DIO","Fiscal de Materia","Defensor del Pueblo","Otros"],columns:[{name:"actions",label:"Acciones",align:"center"},{name:"codigo_ingreso",label:"N° Ingreso",field:"codigo_ingreso",align:"left"},{name:"fecha_hora",label:"Fecha/Hora",align:"left",field:s=>s.fecha_hora},{name:"objeto_ingreso",label:"Objeto ingreso",field:"objeto_ingreso",align:"left"},{name:"cantidad",label:"Hojas",field:"cantidad",align:"center"},{name:"remitente",label:"Remitente",align:"left",field:s=>s.user?s.user.name:s.remitente_otros||s.remitente||""},{name:"disposicion",label:"Disposición",field:"disposicion",align:"left"}]}},mounted(){this.getRemisiones(),this.getUsuarios()},methods:{async getRemisiones(){this.loading=!0;try{const s=await this.$axios.get("remision-casos");this.remisiones=s.data}catch(s){this.$alert&&this.$alert.error(s.response?.data?.message||"Error cargando remisiones")}finally{this.loading=!1}},async getUsuarios(){try{const s=await this.$axios.get("users");this.usuarios=s.data}catch(s){console.error(s),this.$alert&&this.$alert.error("Error cargando usuarios remitentes")}},nuevo(){this.remision={codigo_ingreso:"",fecha_hora:"",objeto_ingreso:"",cantidad:null,remitente:"",organizacion:"",remitente_otros:"",user_id:null,disposicion:"",archivo:null,estado:"ACTIVO"},this.archivoFile=null,this.accion="Nueva",this.dialog=!0},verRemision(s){this.dialogVer=!0,this.remision={...s}},editar(s){this.remision={...s,user_id:s.user_id||null,remitente_otros:s.remitente_otros||""},this.archivoFile=null,this.accion="Editar",this.dialog=!0},async eliminar(s){this.$alert?this.$alert.dialog("¿Desea eliminar la remisión?").onOk(async()=>{this.loading=!0;try{await this.$axios.delete(`remision-casos/${s}`),this.$alert.success("Remisión eliminada"),this.getRemisiones()}catch(e){this.$alert.error(e.response?.data?.message||"No se pudo eliminar")}finally{this.loading=!1}}):console.log("Confirm delete",s)},async guardar(){this.loading=!0;try{const s=new FormData;Object.entries(this.remision).forEach(([d,p])=>{p!=null&&d!=="user"&&d!=="archivo"&&s.append(d,p)}),this.archivoFile&&s.append("archivo",this.archivoFile);let e;this.remision.id?(s.append("_method","PUT"),e=await this.$axios.post(`remision-casos/${this.remision.id}`,s,{headers:{"Content-Type":"multipart/form-data"}}),this.$alert&&this.$alert.success("Remisión actualizada")):(e=await this.$axios.post("remision-casos",s,{headers:{"Content-Type":"multipart/form-data"}}),this.$alert&&this.$alert.success("Remisión creada")),this.dialog=!1,this.getRemisiones()}catch(s){console.error(s),this.$alert&&this.$alert.error(s.response?.data?.message||"No se pudo guardar")}finally{this.loading=!1}},verArchivo(s){if(!s.archivo)return;const d=`${this.$axios.defaults.baseURL.replace("/api/","")}/../storage/${s.archivo}`;window.open(d,"_blank")},imprimir(s){const e=s.fecha_hora||"",d=e.substring(0,10),p=e.substring(11,16)||"",o=s.user?s.user.name:s.remitente_otros||s.remitente||"",a=window.open("","_blank","width=800,height=1000");a.document.write(`
        <html>
        <head>
          <title>Hoja de Ruta</title>
          <style>
            * { box-sizing: border-box; font-family: Arial, sans-serif; }
            body { margin: 20px; }
            .hoja {
              width: 100%;
              border: 1px solid #000;
              padding: 16px;
              font-size: 12px;
            }
            .titulo {
              text-align: center;
              font-weight: bold;
              font-size: 18px;
              margin-bottom: 10px;
            }
            .fila {
              display: flex;
              margin-bottom: 6px;
            }
            .campo {
              border: 1px solid #000;
              padding: 4px;
              flex: 1;
              margin-right: 4px;
            }
            .campo:last-child { margin-right: 0; }
            .label {
              font-weight: bold;
              font-size: 11px;
              display: block;
            }
            .valor {
              min-height: 18px;
            }
            .texto-largo {
              min-height: 40px;
            }
            .seccion-titulo {
              font-weight: bold;
              margin-top: 8px;
              margin-bottom: 4px;
            }
            .lista-proveido {
              margin-top: 6px;
              font-size: 11px;
            }
          </style>
        </head>
        <body onload="window.print();window.close();">
          <div class="hoja">
            <div class="titulo">HOJA DE RUTA</div>

            <div class="fila">
              <div class="campo">
                <span class="label">N° DE INGRESO</span>
                <div class="valor">${s.codigo_ingreso||""}</div>
              </div>
              <div class="campo">
                <span class="label">HOJAS</span>
                <div class="valor">${s.cantidad||""}</div>
              </div>
              <div class="campo">
                <span class="label">FECHA</span>
                <div class="valor">${d}</div>
              </div>
              <div class="campo">
                <span class="label">HORA</span>
                <div class="valor">${p}</div>
              </div>
            </div>

            <div class="campo" style="margin-bottom:6px;">
              <span class="label">REMITENTE FIRMADO POR</span>
              <div class="texto-largo">${o}</div>
            </div>

            <div class="seccion-titulo">OBJETO / INGRESO</div>
            <div class="campo texto-largo" style="margin-bottom:6px;">
              <div>${s.objeto_ingreso||""}</div>
            </div>

            <div class="seccion-titulo">PROVEÍDO / DISPOSICIÓN</div>
            <div class="campo texto-largo">
              <div>${s.disposicion||""}</div>
            </div>

            <div class="lista-proveido">
              <div>☐ Urgente &nbsp;&nbsp; ☐ Atender solicitud &nbsp;&nbsp; ☐ Para su conocimiento</div>
              <div>☐ Preparar informe &nbsp;&nbsp; ☐ Hacer seguimiento &nbsp;&nbsp; ☐ Archivo &nbsp;&nbsp; ☐ Invitación</div>
            </div>
          </div>
        </body>
        </html>
      `),a.document.close()}}},P={class:"text-subtitle1"},S={class:"text-right q-mt-md"},T={key:0,class:"q-mt-sm"};function B(s,e,d,p,o,a){return m(),u(H,{class:"q-pa-md"},{default:n(()=>[i(D,{rows:o.remisiones,columns:o.columns,"row-key":"id",dense:"","wrap-cells":"",flat:"",bordered:"","rows-per-page-options":[0],title:"Remisiones de caso",filter:o.filter},{"top-right":n(()=>[i(g,{color:"positive",label:"Nuevo","no-caps":"",icon:"add_circle_outline",class:"q-mr-sm",loading:o.loading,onClick:a.nuevo},null,8,["loading","onClick"]),i(g,{color:"primary",label:"Actualizar","no-caps":"",icon:"refresh",class:"q-mr-sm",loading:o.loading,onClick:a.getRemisiones},null,8,["loading","onClick"]),i(t,{modelValue:o.filter,"onUpdate:modelValue":e[0]||(e[0]=l=>o.filter=l),label:"Buscar",dense:"",outlined:""},{append:n(()=>[i(c,{name:"search"})]),_:1},8,["modelValue"])]),"body-cell-actions":n(l=>[i(E,{props:l},{default:n(()=>[i(z,{label:"Opciones","no-caps":"",size:"10px",dense:"",color:"primary"},{default:n(()=>[i(I,null,{default:n(()=>[f((m(),u(h,{clickable:"",onClick:y=>a.editar(l.row)},{default:n(()=>[i(r,{avatar:""},{default:n(()=>[i(c,{name:"edit"})]),_:1}),i(r,null,{default:n(()=>[i(V,null,{default:n(()=>e[21]||(e[21]=[v("Editar")])),_:1})]),_:1})]),_:2},1032,["onClick"])),[[x]]),f((m(),u(h,{clickable:"",onClick:y=>a.verRemision(l.row)},{default:n(()=>[i(r,{avatar:""},{default:n(()=>[i(c,{name:"visibility"})]),_:1}),i(r,null,{default:n(()=>[i(V,null,{default:n(()=>e[22]||(e[22]=[v("Ver")])),_:1})]),_:1})]),_:2},1032,["onClick"])),[[x]]),f((m(),u(h,{clickable:"",onClick:y=>a.eliminar(l.row.id)},{default:n(()=>[i(r,{avatar:""},{default:n(()=>[i(c,{name:"delete"})]),_:1}),i(r,null,{default:n(()=>[i(V,null,{default:n(()=>e[23]||(e[23]=[v("Eliminar")])),_:1})]),_:1})]),_:2},1032,["onClick"])),[[x]]),f((m(),u(h,{clickable:"",onClick:y=>a.imprimir(l.row)},{default:n(()=>[i(r,{avatar:""},{default:n(()=>[i(c,{name:"print"})]),_:1}),i(r,null,{default:n(()=>[i(V,null,{default:n(()=>e[24]||(e[24]=[v("Imprimir hoja")])),_:1})]),_:1})]),_:2},1032,["onClick"])),[[x]]),l.row.archivo?f((m(),u(h,{key:0,clickable:"",onClick:y=>a.verArchivo(l.row)},{default:n(()=>[i(r,{avatar:""},{default:n(()=>[i(c,{name:"attach_file"})]),_:1}),i(r,null,{default:n(()=>[i(V,null,{default:n(()=>e[25]||(e[25]=[v("Ver archivo")])),_:1})]),_:1})]),_:2},1032,["onClick"])),[[x]]):k("",!0)]),_:2},1024)]),_:2},1024)]),_:2},1032,["props"])]),_:1},8,["rows","columns","filter"]),i(w,{modelValue:o.dialog,"onUpdate:modelValue":e[11]||(e[11]=l=>o.dialog=l),persistent:""},{default:n(()=>[i(Q,{style:{width:"500px","max-width":"90vw"}},{default:n(()=>[i(C,{class:"row items-center q-pb-none"},{default:n(()=>[b("div",P,q(o.accion)+" remisión",1),i(O),i(g,{icon:"close",flat:"",round:"",dense:"",onClick:e[1]||(e[1]=l=>o.dialog=!1)})]),_:1}),i(C,{class:"q-pt-none"},{default:n(()=>[i(j,{onSubmit:R(a.guardar,["prevent"])},{default:n(()=>[i(_,{modelValue:o.remision.objeto_ingreso,"onUpdate:modelValue":e[2]||(e[2]=l=>o.remision.objeto_ingreso=l),label:"Objeto",options:["Sobre","Hojas","Carpetas"],dense:"",outlined:""},null,8,["modelValue"]),i(t,{modelValue:o.remision.cantidad,"onUpdate:modelValue":e[3]||(e[3]=l=>o.remision.cantidad=l),modelModifiers:{number:!0},label:"Cantidad / Hojas",type:"number",dense:"",outlined:"",class:"q-mt-sm"},null,8,["modelValue"]),i(t,{modelValue:o.remision.remitente,"onUpdate:modelValue":e[4]||(e[4]=l=>o.remision.remitente=l),label:"Remitente",dense:"",outlined:"",class:"q-mt-sm"},null,8,["modelValue"]),i(_,{modelValue:o.remision.organizacion,"onUpdate:modelValue":e[5]||(e[5]=l=>o.remision.organizacion=l),label:"Organización",dense:"",options:o.organizaciones,outlined:"",class:"q-mt-sm"},null,8,["modelValue","options"]),o.remision.organizacion==="Otros"?(m(),u(t,{key:0,modelValue:o.remision.remitente_otros,"onUpdate:modelValue":e[6]||(e[6]=l=>o.remision.remitente_otros=l),label:"Remitente otros (si corresponde)",dense:"",outlined:"",class:"q-mt-sm"},null,8,["modelValue"])):k("",!0),i(_,{modelValue:o.remision.user_id,"onUpdate:modelValue":e[7]||(e[7]=l=>o.remision.user_id=l),options:o.usuarios,"option-value":"id","option-label":l=>`${l.name} (${l.role})`,"emit-value":"","map-options":"",label:"Referencia (usuario)",dense:"",outlined:"",class:"q-mt-sm"},null,8,["modelValue","options","option-label"]),i(F,{modelValue:o.archivoFile,"onUpdate:modelValue":e[8]||(e[8]=l=>o.archivoFile=l),label:"Archivo adjunto (opcional)",outlined:"",dense:"",class:"q-mt-sm",clearable:!0,accept:".pdf,.jpg,.jpeg,.png,.doc,.docx","use-chips":""},{append:n(()=>[i(c,{name:"attach_file"})]),_:1},8,["modelValue"]),i(_,{modelValue:o.remision.disposicion,"onUpdate:modelValue":e[9]||(e[9]=l=>o.remision.disposicion=l),options:o.dispociciones,label:"Disposición / Proveído",dense:"",outlined:"",class:"q-mt-sm"},null,8,["modelValue","options"]),b("div",S,[i(g,{flat:"",color:"negative",label:"Cancelar","no-caps":"",onClick:e[10]||(e[10]=l=>o.dialog=!1),loading:o.loading},null,8,["loading"]),i(g,{color:"primary",label:"Guardar","no-caps":"",type:"submit",class:"q-ml-sm",loading:o.loading},null,8,["loading"])])]),_:1},8,["onSubmit"])]),_:1})]),_:1})]),_:1},8,["modelValue"]),i(w,{modelValue:o.dialogVer,"onUpdate:modelValue":e[20]||(e[20]=l=>o.dialogVer=l),persistent:""},{default:n(()=>[i(Q,{style:{width:"500px","max-width":"90vw"}},{default:n(()=>[i(C,{class:"row items-center q-pb-none"},{default:n(()=>[e[26]||(e[26]=b("div",{class:"text-subtitle1"},"Ver remisión",-1)),i(O),i(g,{icon:"close",flat:"",round:"",dense:"",onClick:e[12]||(e[12]=l=>o.dialogVer=!1)})]),_:1}),i(C,{class:"q-pt-none"},{default:n(()=>[i(j,null,{default:n(()=>[i(t,{modelValue:o.remision.codigo_ingreso,"onUpdate:modelValue":e[13]||(e[13]=l=>o.remision.codigo_ingreso=l),label:"N° Ingreso",dense:"",outlined:"",readonly:""},null,8,["modelValue"]),i(t,{modelValue:o.remision.fecha_hora,"onUpdate:modelValue":e[14]||(e[14]=l=>o.remision.fecha_hora=l),label:"Fecha/Hora",dense:"",outlined:"",readonly:"",class:"q-mt-sm"},null,8,["modelValue"]),i(t,{modelValue:o.remision.objeto_ingreso,"onUpdate:modelValue":e[15]||(e[15]=l=>o.remision.objeto_ingreso=l),label:"Objeto ingreso",dense:"",outlined:"",readonly:"",class:"q-mt-sm"},null,8,["modelValue"]),i(t,{modelValue:o.remision.cantidad,"onUpdate:modelValue":e[16]||(e[16]=l=>o.remision.cantidad=l),label:"Cantidad / Hojas",dense:"",outlined:"",readonly:"",class:"q-mt-sm"},null,8,["modelValue"]),i(t,{modelValue:o.remision.organizacion,"onUpdate:modelValue":e[17]||(e[17]=l=>o.remision.organizacion=l),label:"Organización",dense:"",outlined:"",readonly:"",class:"q-mt-sm"},null,8,["modelValue"]),i(t,{modelValue:o.remision.disposicion,"onUpdate:modelValue":e[18]||(e[18]=l=>o.remision.disposicion=l),label:"Disposición / Proveído",dense:"",outlined:"",readonly:"",class:"q-mt-sm"},null,8,["modelValue"]),o.remision.archivo?(m(),A("div",T,[e[27]||(e[27]=b("strong",null,"Archivo adjunto:",-1)),b("a",{href:"#",onClick:e[19]||(e[19]=R(l=>a.verArchivo(o.remision),["prevent"]))},"Ver archivo")])):k("",!0)]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue"])]),_:1})}const ae=U(N,[["render",B]]);export{ae as default};
