import{_ as q,a0 as R,o as m,a as o,w as n,V as v,$ as C,c,d as p,Z as b,f,a6 as t,Q as _,e as k,b as u,t as O,af as Q,h as E,a1 as A}from"./index-07_X1hqC.js";import{Q as h,a as r}from"./QItem-DnWPKqFP.js";import{Q as V}from"./QItemLabel-Dj13st_M.js";import{Q as I}from"./QList-BGKkqCS-.js";import{Q as D}from"./QBtnDropdown-BdxG2fY7.js";import{a as z,Q as N}from"./QTable-CB7Pd644.js";import{Q as U}from"./QSpace-DwvuL4jz.js";import{Q as w}from"./QSelect-c6bTY1Yw.js";import{Q as F}from"./QFile-CL8VmjdZ.js";import{Q as j}from"./QForm-B694N49q.js";import{C as x}from"./ClosePopup-YNzNHQDw.js";import"./QBtnGroup-D19N9wPo.js";import"./QMenu-DTWjuNzv.js";import"./position-engine-B5Gd_Ixr.js";import"./QMarkupTable-D9vXMovn.js";import"./use-fullscreen-aMf2bwB0.js";import"./format-CJebrXOQ.js";const S={name:"RemisionCasosPage",props:{interoExterno:{type:String,default:"EXTERNO"}},data(){return{remisiones:[],remision:{},dialogVer:!1,dialog:!1,accion:"",loading:!1,filter:"",archivoFile:null,usuarios:[],dispociciones:["Urgente","Atender Solicitud","Para su conocimiento","Preparar Informe","Hacer seguimiento","Archivo","Invitación"],organizaciones:["Secretario Municipal de Desarrollo Humano","Alcalde Municipal GAMO","DIO","Fiscal de Materia","Defensor del Pueblo","Otros"],columns:[{name:"actions",label:"Acciones",align:"center"},{name:"codigo_ingreso",label:"N° Ingreso",field:"codigo_ingreso",align:"left"},{name:"fecha_hora",label:"Fecha/Hora",align:"left",field:s=>s.fecha_hora},{name:"objeto_ingreso",label:"Objeto ingreso",field:"objeto_ingreso",align:"left"},{name:"cantidad",label:"Hojas",field:"cantidad",align:"center"},{name:"remitente",label:"Remitente",align:"left",field:s=>s.user?s.user.name:s.remitente_otros||s.remitente||""},{name:"disposicion",label:"Disposición",field:"disposicion",align:"left"}]}},mounted(){this.getRemisiones(),this.getUsuarios()},methods:{async getRemisiones(){this.loading=!0;try{const s=await this.$axios.get("remision-casos",{params:{interoExterno:this.interoExterno}});this.remisiones=s.data}catch(s){this.$alert&&this.$alert.error(s.response?.data?.message||"Error cargando remisiones")}finally{this.loading=!1}},async getUsuarios(){try{const s=await this.$axios.get("users");this.usuarios=s.data}catch(s){console.error(s),this.$alert&&this.$alert.error("Error cargando usuarios remitentes")}},nuevo(){this.remision={codigo_ingreso:"",fecha_hora:"",objeto_ingreso:"",cantidad:null,remitente:"",organizacion:"",remitente_otros:"",user_id:null,disposicion:"",archivo:null,estado:"ACTIVO"},console.log("user store:",this.$store.user),this.remision.remitente=this.$store.user.name||"",this.archivoFile=null,this.accion="Nueva",this.dialog=!0},verRemision(s){this.dialogVer=!0,this.remision={...s}},editar(s){this.remision={...s,user_id:s.user_id||null,remitente_otros:s.remitente_otros||""},this.archivoFile=null,this.accion="Editar",this.dialog=!0},async eliminar(s){this.$alert?this.$alert.dialog("¿Desea eliminar la remisión?").onOk(async()=>{this.loading=!0;try{await this.$axios.delete(`remision-casos/${s}`),this.$alert.success("Remisión eliminada"),this.getRemisiones()}catch(e){this.$alert.error(e.response?.data?.message||"No se pudo eliminar")}finally{this.loading=!1}}):console.log("Confirm delete",s)},async guardar(){this.loading=!0;try{const s=new FormData;Object.entries(this.remision).forEach(([d,g])=>{g!=null&&d!=="user"&&d!=="archivo"&&s.append(d,g)}),s.append("interno_externo",this.interoExterno),this.archivoFile&&s.append("archivo",this.archivoFile);let e;this.remision.id?(s.append("_method","PUT"),e=await this.$axios.post(`remision-casos/${this.remision.id}`,s,{headers:{"Content-Type":"multipart/form-data"}}),this.$alert&&this.$alert.success("Remisión actualizada")):(e=await this.$axios.post("remision-casos",s,{headers:{"Content-Type":"multipart/form-data"}}),this.$alert&&this.$alert.success("Remisión creada")),this.dialog=!1,this.getRemisiones()}catch(s){console.error(s),this.$alert&&this.$alert.error(s.response?.data?.message||"No se pudo guardar")}finally{this.loading=!1}},verArchivo(s){if(!s.archivo)return;const d=`${this.$axios.defaults.baseURL.replace("/api/","")}/../storage/${s.archivo}`;window.open(d,"_blank")},imprimir(s){const e=s.fecha_hora||"",d=e.substring(0,10),g=e.substring(11,16)||"",i=s.user?s.user.name:s.remitente_otros||s.remitente||"",a=window.open("","_blank","width=800,height=1000");a.document.write(`
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
                <div class="valor">${g}</div>
              </div>
            </div>

            <div class="campo" style="margin-bottom:6px;">
              <span class="label">REMITENTE FIRMADO POR</span>
              <div class="texto-largo">${i}</div>
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
      `),a.document.close()}}},H={class:"text-subtitle1"},P={class:"text-right q-mt-md"},T={class:"q-mt-sm"},B={key:0,class:"q-mt-sm"};function M(s,e,d,g,i,a){return m(),R(A,null,[o(N,{rows:i.remisiones,columns:i.columns,"row-key":"id",dense:"","wrap-cells":"",flat:"",bordered:"","rows-per-page-options":[0],title:"Remisiones de caso",filter:i.filter},{"top-right":n(()=>[o(f,{color:"positive",label:"Nuevo","no-caps":"",icon:"add_circle_outline",class:"q-mr-sm",loading:i.loading,onClick:a.nuevo},null,8,["loading","onClick"]),o(f,{color:"primary",label:"Actualizar","no-caps":"",icon:"refresh",class:"q-mr-sm",loading:i.loading,onClick:a.getRemisiones},null,8,["loading","onClick"]),o(t,{modelValue:i.filter,"onUpdate:modelValue":e[0]||(e[0]=l=>i.filter=l),label:"Buscar",dense:"",outlined:""},{append:n(()=>[o(p,{name:"search"})]),_:1},8,["modelValue"])]),"body-cell-actions":n(l=>[o(z,{props:l},{default:n(()=>[o(D,{label:"Opciones","no-caps":"",size:"10px",dense:"",color:"primary"},{default:n(()=>[o(I,null,{default:n(()=>[v((m(),c(h,{clickable:"",onClick:y=>a.editar(l.row)},{default:n(()=>[o(r,{avatar:""},{default:n(()=>[o(p,{name:"edit"})]),_:1}),o(r,null,{default:n(()=>[o(V,null,{default:n(()=>e[23]||(e[23]=[b("Editar")])),_:1})]),_:1})]),_:2},1032,["onClick"])),[[x]]),v((m(),c(h,{clickable:"",onClick:y=>a.verRemision(l.row)},{default:n(()=>[o(r,{avatar:""},{default:n(()=>[o(p,{name:"visibility"})]),_:1}),o(r,null,{default:n(()=>[o(V,null,{default:n(()=>e[24]||(e[24]=[b("Ver")])),_:1})]),_:1})]),_:2},1032,["onClick"])),[[x]]),v((m(),c(h,{clickable:"",onClick:y=>a.eliminar(l.row.id)},{default:n(()=>[o(r,{avatar:""},{default:n(()=>[o(p,{name:"delete"})]),_:1}),o(r,null,{default:n(()=>[o(V,null,{default:n(()=>e[25]||(e[25]=[b("Eliminar")])),_:1})]),_:1})]),_:2},1032,["onClick"])),[[x]]),v((m(),c(h,{clickable:"",onClick:y=>a.imprimir(l.row)},{default:n(()=>[o(r,{avatar:""},{default:n(()=>[o(p,{name:"print"})]),_:1}),o(r,null,{default:n(()=>[o(V,null,{default:n(()=>e[26]||(e[26]=[b("Imprimir hoja")])),_:1})]),_:1})]),_:2},1032,["onClick"])),[[x]]),l.row.archivo?v((m(),c(h,{key:0,clickable:"",onClick:y=>a.verArchivo(l.row)},{default:n(()=>[o(r,{avatar:""},{default:n(()=>[o(p,{name:"attach_file"})]),_:1}),o(r,null,{default:n(()=>[o(V,null,{default:n(()=>e[27]||(e[27]=[b("Ver archivo")])),_:1})]),_:1})]),_:2},1032,["onClick"])),[[x]]):C("",!0)]),_:2},1024)]),_:2},1024)]),_:2},1032,["props"])]),_:1},8,["rows","columns","filter"]),o(E,{modelValue:i.dialog,"onUpdate:modelValue":e[12]||(e[12]=l=>i.dialog=l),persistent:""},{default:n(()=>[o(_,{style:{width:"500px","max-width":"90vw"}},{default:n(()=>[o(k,{class:"row items-center q-pb-none"},{default:n(()=>[u("div",H,O(i.accion)+" remisión",1),o(U),o(f,{icon:"close",flat:"",round:"",dense:"",onClick:e[1]||(e[1]=l=>i.dialog=!1)})]),_:1}),o(k,{class:"q-pt-none"},{default:n(()=>[o(j,{onSubmit:Q(a.guardar,["prevent"])},{default:n(()=>[o(w,{clearable:"",modelValue:i.remision.objeto_ingreso,"onUpdate:modelValue":e[2]||(e[2]=l=>i.remision.objeto_ingreso=l),label:"Objeto",options:["Sobre","Hojas","Carpetas"],dense:"",outlined:""},null,8,["modelValue"]),o(t,{modelValue:i.remision.cantidad,"onUpdate:modelValue":e[3]||(e[3]=l=>i.remision.cantidad=l),modelModifiers:{number:!0},label:"Cantidad / Hojas",type:"number",dense:"",outlined:"",class:"q-mt-sm"},null,8,["modelValue"]),o(t,{modelValue:i.remision.remitente,"onUpdate:modelValue":e[4]||(e[4]=l=>i.remision.remitente=l),label:"Remitente",dense:"",outlined:"",class:"q-mt-sm"},null,8,["modelValue"]),d.interoExterno==="EXTERNO"?(m(),c(w,{key:0,clearable:"",modelValue:i.remision.organizacion,"onUpdate:modelValue":e[5]||(e[5]=l=>i.remision.organizacion=l),label:"Organización",dense:"",options:i.organizaciones,outlined:"",class:"q-mt-sm"},null,8,["modelValue","options"])):C("",!0),i.remision.organizacion==="Otros"?(m(),c(t,{key:1,modelValue:i.remision.remitente_otros,"onUpdate:modelValue":e[6]||(e[6]=l=>i.remision.remitente_otros=l),label:"Remitente otros (si corresponde)",dense:"",outlined:"",class:"q-mt-sm"},null,8,["modelValue"])):C("",!0),o(w,{clearable:"",modelValue:i.remision.user_id,"onUpdate:modelValue":e[7]||(e[7]=l=>i.remision.user_id=l),options:i.usuarios,"option-value":"id","option-label":l=>`${l.name} (${l.role})`,"emit-value":"","map-options":"",label:"Remitido A",dense:"",outlined:"",class:"q-mt-sm"},null,8,["modelValue","options","option-label"]),o(F,{modelValue:i.archivoFile,"onUpdate:modelValue":e[8]||(e[8]=l=>i.archivoFile=l),label:"Archivo adjunto (opcional)",outlined:"",dense:"",class:"q-mt-sm",clearable:!0,accept:".pdf,.jpg,.jpeg,.png,.doc,.docx","use-chips":""},{append:n(()=>[o(p,{name:"attach_file"})]),_:1},8,["modelValue"]),o(w,{clearable:"",modelValue:i.remision.disposicion,"onUpdate:modelValue":e[9]||(e[9]=l=>i.remision.disposicion=l),options:i.dispociciones,label:"Disposición / Proveído",dense:"",outlined:"",class:"q-mt-sm"},null,8,["modelValue","options"]),o(t,{modelValue:i.remision.descripcion,"onUpdate:modelValue":e[10]||(e[10]=l=>i.remision.descripcion=l),label:"Descripción",type:"textarea",dense:"",outlined:"",class:"q-mt-sm"},null,8,["modelValue"]),u("div",P,[o(f,{flat:"",color:"negative",label:"Cancelar","no-caps":"",onClick:e[11]||(e[11]=l=>i.dialog=!1),loading:i.loading},null,8,["loading"]),o(f,{color:"primary",label:"Guardar","no-caps":"",type:"submit",class:"q-ml-sm",loading:i.loading},null,8,["loading"])])]),_:1},8,["onSubmit"])]),_:1})]),_:1})]),_:1},8,["modelValue"]),o(E,{modelValue:i.dialogVer,"onUpdate:modelValue":e[22]||(e[22]=l=>i.dialogVer=l),persistent:""},{default:n(()=>[o(_,{style:{width:"500px","max-width":"90vw"}},{default:n(()=>[o(k,{class:"row items-center q-pb-none"},{default:n(()=>[e[28]||(e[28]=u("div",{class:"text-subtitle1"},"Ver remisión",-1)),o(U),o(f,{icon:"close",flat:"",round:"",dense:"",onClick:e[13]||(e[13]=l=>i.dialogVer=!1)})]),_:1}),o(k,{class:"q-pt-none"},{default:n(()=>[o(j,null,{default:n(()=>[o(t,{modelValue:i.remision.codigo_ingreso,"onUpdate:modelValue":e[14]||(e[14]=l=>i.remision.codigo_ingreso=l),label:"N° Ingreso",dense:"",outlined:"",readonly:""},null,8,["modelValue"]),o(t,{modelValue:i.remision.fecha_hora,"onUpdate:modelValue":e[15]||(e[15]=l=>i.remision.fecha_hora=l),label:"Fecha/Hora",dense:"",outlined:"",readonly:"",class:"q-mt-sm"},null,8,["modelValue"]),o(t,{modelValue:i.remision.objeto_ingreso,"onUpdate:modelValue":e[16]||(e[16]=l=>i.remision.objeto_ingreso=l),label:"Objeto ingreso",dense:"",outlined:"",readonly:"",class:"q-mt-sm"},null,8,["modelValue"]),o(t,{modelValue:i.remision.cantidad,"onUpdate:modelValue":e[17]||(e[17]=l=>i.remision.cantidad=l),label:"Cantidad / Hojas",dense:"",outlined:"",readonly:"",class:"q-mt-sm"},null,8,["modelValue"]),o(t,{modelValue:i.remision.remitente,"onUpdate:modelValue":e[18]||(e[18]=l=>i.remision.remitente=l),label:"Remitente",dense:"",outlined:"",readonly:"",class:"q-mt-sm"},null,8,["modelValue"]),o(t,{modelValue:i.remision.organizacion,"onUpdate:modelValue":e[19]||(e[19]=l=>i.remision.organizacion=l),label:"Organización",dense:"",outlined:"",readonly:"",class:"q-mt-sm"},null,8,["modelValue"]),o(t,{modelValue:i.remision.disposicion,"onUpdate:modelValue":e[20]||(e[20]=l=>i.remision.disposicion=l),label:"Disposición / Proveído",dense:"",outlined:"",readonly:"",class:"q-mt-sm"},null,8,["modelValue"]),u("div",T,[e[29]||(e[29]=u("strong",null,"Referencia:",-1)),u("div",null,O(i.remision.user?i.remision.user.name+" ("+i.remision.user.role+")":""),1)]),i.remision.archivo?(m(),R("div",B,[e[30]||(e[30]=u("strong",null,"Archivo adjunto:",-1)),u("a",{href:"#",onClick:e[21]||(e[21]=Q(l=>a.verArchivo(i.remision),["prevent"]))},"Ver archivo")])):C("",!0)]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue"])],64)}const re=q(S,[["render",M]]);export{re as default};
