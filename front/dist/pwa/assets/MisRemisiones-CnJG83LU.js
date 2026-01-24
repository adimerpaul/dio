import{_ as D,a0 as h,o as d,b as t,a as s,f,w as n,a1 as j,a2 as I,bl as z,V,$ as v,c as g,d as b,Z as x,t as u,Q,e as y,af as q,a6 as a,h as U}from"./index-07_X1hqC.js";import{Q as C,a as c}from"./QItem-DnWPKqFP.js";import{Q as _}from"./QItemLabel-Dj13st_M.js";import{Q as N}from"./QList-BGKkqCS-.js";import{Q as F}from"./QBtnDropdown-BdxG2fY7.js";import{Q as H}from"./QMarkupTable-D9vXMovn.js";import{Q as E}from"./QSpace-DwvuL4jz.js";import{Q as R}from"./QSelect-c6bTY1Yw.js";import{Q as S}from"./QFile-CL8VmjdZ.js";import{Q as A}from"./QForm-B694N49q.js";import{C as k}from"./ClosePopup-YNzNHQDw.js";import"./QBtnGroup-D19N9wPo.js";import"./QMenu-DTWjuNzv.js";import"./position-engine-B5Gd_Ixr.js";import"./format-CJebrXOQ.js";const P={name:"RemisionCasosPage",props:{interoExterno:{type:String,default:"EXTERNO"}},data(){return{remisiones:[],remision:{},dialogVer:!1,dialog:!1,accion:"",loading:!1,filter:"",archivoFile:null,usuarios:[],dispociciones:["Urgente","Atender Solicitud","Para su conocimiento","Preparar Informe","Hacer seguimiento","Archivo","Invitación"],organizaciones:["Secretario Municipal de Desarrollo Humano","Alcalde Municipal GAMO","DIO","Fiscal de Materia","Defensor del Pueblo","Otros"],columns:[{name:"actions",label:"Acciones",align:"center"},{name:"codigo_ingreso",label:"N° Ingreso",field:"codigo_ingreso",align:"left"},{name:"fecha_hora",label:"Fecha/Hora",align:"left",field:l=>l.fecha_hora},{name:"objeto_ingreso",label:"Objeto ingreso",field:"objeto_ingreso",align:"left"},{name:"cantidad",label:"Hojas",field:"cantidad",align:"center"},{name:"remitente",label:"Remitente",align:"left",field:l=>l.user?l.user.name:l.remitente_otros||l.remitente||""},{name:"disposicion",label:"Disposición",field:"disposicion",align:"left"}]}},mounted(){this.getRemisiones(),this.getUsuarios()},methods:{async getRemisiones(){this.loading=!0;try{const l=await this.$axios.get("mis-remision-casos",{params:{interoExterno:this.interoExterno}});this.remisiones=l.data}catch(l){this.$alert&&this.$alert.error(l.response?.data?.message||"Error cargando remisiones")}finally{this.loading=!1}},async getUsuarios(){try{const l=await this.$axios.get("users");this.usuarios=l.data}catch(l){console.error(l),this.$alert&&this.$alert.error("Error cargando usuarios remitentes")}},nuevo(){this.remision={codigo_ingreso:"",fecha_hora:"",objeto_ingreso:"",cantidad:null,remitente:"",organizacion:"",remitente_otros:"",user_id:null,disposicion:"",archivo:null,estado:"ACTIVO"},console.log("user store:",this.$store.user),this.remision.remitente=this.$store.user.name||"",this.archivoFile=null,this.accion="Nueva",this.dialog=!0},verRemision(l){this.dialogVer=!0,this.remision={...l}},editar(l){this.remision={...l,user_id:l.user_id||null,remitente_otros:l.remitente_otros||""},this.archivoFile=null,this.accion="Editar",this.dialog=!0},async eliminar(l){this.$alert?this.$alert.dialog("¿Desea eliminar la remisión?").onOk(async()=>{this.loading=!0;try{await this.$axios.delete(`remision-casos/${l}`),this.$alert.success("Remisión eliminada"),this.getRemisiones()}catch(e){this.$alert.error(e.response?.data?.message||"No se pudo eliminar")}finally{this.loading=!1}}):console.log("Confirm delete",l)},async guardar(){this.loading=!0;try{const l=new FormData;Object.entries(this.remision).forEach(([m,p])=>{p!=null&&m!=="user"&&m!=="archivo"&&l.append(m,p)}),l.append("interno_externo",this.interoExterno),this.archivoFile&&l.append("archivo",this.archivoFile);let e;this.remision.id?(l.append("_method","PUT"),e=await this.$axios.post(`remision-casos/${this.remision.id}`,l,{headers:{"Content-Type":"multipart/form-data"}}),this.$alert&&this.$alert.success("Remisión actualizada")):(e=await this.$axios.post("remision-casos",l,{headers:{"Content-Type":"multipart/form-data"}}),this.$alert&&this.$alert.success("Remisión creada")),this.dialog=!1,this.getRemisiones()}catch(l){console.error(l),this.$alert&&this.$alert.error(l.response?.data?.message||"No se pudo guardar")}finally{this.loading=!1}},verArchivo(l){if(!l.archivo)return;const m=`${this.$axios.defaults.baseURL.replace("/api/","")}/../storage/${l.archivo}`;window.open(m,"_blank")},leerCaso(l){this.$alert?this.$alert.dialog("¿Desea marcar esta remisión como leída?").onOk(async()=>{this.loading=!0;try{await this.$axios.post(`mis-remision-casos/${l.id}/marcar-leido`),this.$alert.success("Remisión marcada como leída"),this.getRemisiones()}catch(e){this.$alert.error(e.response?.data?.message||"No se pudo marcar como leído")}finally{this.loading=!1}}):console.log("Confirm leer caso",l.id)},imprimir(l){const e=l.fecha_hora||"",m=e.substring(0,10),p=e.substring(11,16)||"",o=l.user?l.user.name:l.remitente_otros||l.remitente||"",r=window.open("","_blank","width=800,height=1000");r.document.write(`
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
                <div class="valor">${l.codigo_ingreso||""}</div>
              </div>
              <div class="campo">
                <span class="label">HOJAS</span>
                <div class="valor">${l.cantidad||""}</div>
              </div>
              <div class="campo">
                <span class="label">FECHA</span>
                <div class="valor">${m}</div>
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
              <div>${l.objeto_ingreso||""}</div>
            </div>

            <div class="seccion-titulo">PROVEÍDO / DISPOSICIÓN</div>
            <div class="campo texto-largo">
              <div>${l.disposicion||""}</div>
            </div>

            <div class="lista-proveido">
              <div>☐ Urgente &nbsp;&nbsp; ☐ Atender solicitud &nbsp;&nbsp; ☐ Para su conocimiento</div>
              <div>☐ Preparar informe &nbsp;&nbsp; ☐ Hacer seguimiento &nbsp;&nbsp; ☐ Archivo &nbsp;&nbsp; ☐ Invitación</div>
            </div>
          </div>
        </body>
        </html>
      `),r.document.close()}}},T={class:"q-pa-md"},M={class:"text-right"},B={class:"text-center"},w={class:"text-subtitle1"},G={class:"text-right q-mt-md"},L={class:"q-mt-sm"},J={key:0,class:"q-mt-sm"};function X(l,e,m,p,o,r){return d(),h(j,null,[t("div",T,[e[27]||(e[27]=t("div",{class:"text-h6"}," Mis Remisiones ",-1)),t("div",M,[s(f,{color:"primary",label:"Actualizar","no-caps":"",icon:"refresh",class:"q-mr-sm q-mt-sm",loading:o.loading,onClick:r.getRemisiones},null,8,["loading","onClick"])]),s(H,{dense:"",flat:"",bordered:"","wrap-cells":""},{default:n(()=>[e[26]||(e[26]=t("thead",null,[t("tr",{class:"bg-primary text-white"},[t("th",null,"Acciones"),t("th",null,"N° Ingreso"),t("th",null,"Fecha/Hora"),t("th",null,"Objeto ingreso"),t("th",null,"Hojas"),t("th",null,"Remitente"),t("th",null,"Disposición")])],-1)),t("tbody",null,[(d(!0),h(j,null,I(o.remisiones,i=>(d(),h("tr",{key:i.id,class:z({"bg-red-4":i.fechaleido==null})},[t("td",null,[s(F,{label:"Opciones","no-caps":"",size:"10px",dense:"",color:"primary"},{default:n(()=>[s(N,null,{default:n(()=>[V((d(),g(C,{clickable:"",onClick:O=>r.verRemision(i)},{default:n(()=>[s(c,{avatar:""},{default:n(()=>[s(b,{name:"visibility"})]),_:1}),s(c,null,{default:n(()=>[s(_,null,{default:n(()=>e[22]||(e[22]=[x("Ver")])),_:1})]),_:1})]),_:2},1032,["onClick"])),[[k]]),V((d(),g(C,{clickable:"",onClick:O=>r.imprimir(i)},{default:n(()=>[s(c,{avatar:""},{default:n(()=>[s(b,{name:"print"})]),_:1}),s(c,null,{default:n(()=>[s(_,null,{default:n(()=>e[23]||(e[23]=[x("Imprimir hoja")])),_:1})]),_:1})]),_:2},1032,["onClick"])),[[k]]),i.archivo?V((d(),g(C,{key:0,clickable:"",onClick:O=>r.verArchivo(i)},{default:n(()=>[s(c,{avatar:""},{default:n(()=>[s(b,{name:"attach_file"})]),_:1}),s(c,null,{default:n(()=>[s(_,null,{default:n(()=>e[24]||(e[24]=[x("Ver archivo")])),_:1})]),_:1})]),_:2},1032,["onClick"])),[[k]]):v("",!0),i.fechaleido==null?V((d(),g(C,{key:1,clickable:"",onClick:O=>r.leerCaso(i)},{default:n(()=>[s(c,{avatar:""},{default:n(()=>[s(b,{name:"mark_email_read"})]),_:1}),s(c,null,{default:n(()=>[s(_,null,{default:n(()=>e[25]||(e[25]=[x("Marcar como leído")])),_:1})]),_:1})]),_:2},1032,["onClick"])),[[k]]):v("",!0)]),_:2},1024)]),_:2},1024)]),t("td",null,u(i.codigo_ingreso),1),t("td",null,u(i.fecha_hora),1),t("td",null,u(i.objeto_ingreso),1),t("td",B,u(i.cantidad),1),t("td",null,u(i.user?i.user.name:i.remitente_otros||i.remitente||""),1),t("td",null,u(i.disposicion),1)],2))),128))])]),_:1})]),s(U,{modelValue:o.dialog,"onUpdate:modelValue":e[11]||(e[11]=i=>o.dialog=i),persistent:""},{default:n(()=>[s(Q,{style:{width:"500px","max-width":"90vw"}},{default:n(()=>[s(y,{class:"row items-center q-pb-none"},{default:n(()=>[t("div",w,u(o.accion)+" remisión",1),s(E),s(f,{icon:"close",flat:"",round:"",dense:"",onClick:e[0]||(e[0]=i=>o.dialog=!1)})]),_:1}),s(y,{class:"q-pt-none"},{default:n(()=>[s(A,{onSubmit:q(r.guardar,["prevent"])},{default:n(()=>[s(R,{clearable:"",modelValue:o.remision.objeto_ingreso,"onUpdate:modelValue":e[1]||(e[1]=i=>o.remision.objeto_ingreso=i),label:"Objeto",options:["Sobre","Hojas","Carpetas"],dense:"",outlined:""},null,8,["modelValue"]),s(a,{modelValue:o.remision.cantidad,"onUpdate:modelValue":e[2]||(e[2]=i=>o.remision.cantidad=i),modelModifiers:{number:!0},label:"Cantidad / Hojas",type:"number",dense:"",outlined:"",class:"q-mt-sm"},null,8,["modelValue"]),s(a,{modelValue:o.remision.remitente,"onUpdate:modelValue":e[3]||(e[3]=i=>o.remision.remitente=i),label:"Remitente",dense:"",outlined:"",class:"q-mt-sm"},null,8,["modelValue"]),m.interoExterno==="EXTERNO"?(d(),g(R,{key:0,clearable:"",modelValue:o.remision.organizacion,"onUpdate:modelValue":e[4]||(e[4]=i=>o.remision.organizacion=i),label:"Organización",dense:"",options:o.organizaciones,outlined:"",class:"q-mt-sm"},null,8,["modelValue","options"])):v("",!0),o.remision.organizacion==="Otros"?(d(),g(a,{key:1,modelValue:o.remision.remitente_otros,"onUpdate:modelValue":e[5]||(e[5]=i=>o.remision.remitente_otros=i),label:"Remitente otros (si corresponde)",dense:"",outlined:"",class:"q-mt-sm"},null,8,["modelValue"])):v("",!0),s(R,{clearable:"",modelValue:o.remision.user_id,"onUpdate:modelValue":e[6]||(e[6]=i=>o.remision.user_id=i),options:o.usuarios,"option-value":"id","option-label":i=>`${i.name} (${i.role})`,"emit-value":"","map-options":"",label:"Remitido A",dense:"",outlined:"",class:"q-mt-sm"},null,8,["modelValue","options","option-label"]),s(S,{modelValue:o.archivoFile,"onUpdate:modelValue":e[7]||(e[7]=i=>o.archivoFile=i),label:"Archivo adjunto (opcional)",outlined:"",dense:"",class:"q-mt-sm",clearable:!0,accept:".pdf,.jpg,.jpeg,.png,.doc,.docx","use-chips":""},{append:n(()=>[s(b,{name:"attach_file"})]),_:1},8,["modelValue"]),s(R,{clearable:"",modelValue:o.remision.disposicion,"onUpdate:modelValue":e[8]||(e[8]=i=>o.remision.disposicion=i),options:o.dispociciones,label:"Disposición / Proveído",dense:"",outlined:"",class:"q-mt-sm"},null,8,["modelValue","options"]),s(a,{modelValue:o.remision.descripcion,"onUpdate:modelValue":e[9]||(e[9]=i=>o.remision.descripcion=i),label:"Descripción",type:"textarea",dense:"",outlined:"",class:"q-mt-sm"},null,8,["modelValue"]),t("div",G,[s(f,{flat:"",color:"negative",label:"Cancelar","no-caps":"",onClick:e[10]||(e[10]=i=>o.dialog=!1),loading:o.loading},null,8,["loading"]),s(f,{color:"primary",label:"Guardar","no-caps":"",type:"submit",class:"q-ml-sm",loading:o.loading},null,8,["loading"])])]),_:1},8,["onSubmit"])]),_:1})]),_:1})]),_:1},8,["modelValue"]),s(U,{modelValue:o.dialogVer,"onUpdate:modelValue":e[21]||(e[21]=i=>o.dialogVer=i),persistent:""},{default:n(()=>[s(Q,{style:{width:"500px","max-width":"90vw"}},{default:n(()=>[s(y,{class:"row items-center q-pb-none"},{default:n(()=>[e[28]||(e[28]=t("div",{class:"text-subtitle1"},"Ver remisión",-1)),s(E),s(f,{icon:"close",flat:"",round:"",dense:"",onClick:e[12]||(e[12]=i=>o.dialogVer=!1)})]),_:1}),s(y,{class:"q-pt-none"},{default:n(()=>[s(A,null,{default:n(()=>[s(a,{modelValue:o.remision.codigo_ingreso,"onUpdate:modelValue":e[13]||(e[13]=i=>o.remision.codigo_ingreso=i),label:"N° Ingreso",dense:"",outlined:"",readonly:""},null,8,["modelValue"]),s(a,{modelValue:o.remision.fecha_hora,"onUpdate:modelValue":e[14]||(e[14]=i=>o.remision.fecha_hora=i),label:"Fecha/Hora",dense:"",outlined:"",readonly:"",class:"q-mt-sm"},null,8,["modelValue"]),s(a,{modelValue:o.remision.objeto_ingreso,"onUpdate:modelValue":e[15]||(e[15]=i=>o.remision.objeto_ingreso=i),label:"Objeto ingreso",dense:"",outlined:"",readonly:"",class:"q-mt-sm"},null,8,["modelValue"]),s(a,{modelValue:o.remision.cantidad,"onUpdate:modelValue":e[16]||(e[16]=i=>o.remision.cantidad=i),label:"Cantidad / Hojas",dense:"",outlined:"",readonly:"",class:"q-mt-sm"},null,8,["modelValue"]),s(a,{modelValue:o.remision.remitente,"onUpdate:modelValue":e[17]||(e[17]=i=>o.remision.remitente=i),label:"Remitente",dense:"",outlined:"",readonly:"",class:"q-mt-sm"},null,8,["modelValue"]),s(a,{modelValue:o.remision.organizacion,"onUpdate:modelValue":e[18]||(e[18]=i=>o.remision.organizacion=i),label:"Organización",dense:"",outlined:"",readonly:"",class:"q-mt-sm"},null,8,["modelValue"]),s(a,{modelValue:o.remision.disposicion,"onUpdate:modelValue":e[19]||(e[19]=i=>o.remision.disposicion=i),label:"Disposición / Proveído",dense:"",outlined:"",readonly:"",class:"q-mt-sm"},null,8,["modelValue"]),t("div",L,[e[29]||(e[29]=t("strong",null,"Referencia:",-1)),t("div",null,u(o.remision.user?o.remision.user.name+" ("+o.remision.user.role+")":""),1)]),o.remision.archivo?(d(),h("div",J,[e[30]||(e[30]=t("strong",null,"Archivo adjunto:",-1)),t("a",{href:"#",onClick:e[20]||(e[20]=q(i=>r.verArchivo(o.remision),["prevent"]))},"Ver archivo")])):v("",!0)]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue"])],64)}const me=D(P,[["render",X]]);export{me as default};
