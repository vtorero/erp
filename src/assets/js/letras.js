function unidades(num) {
    switch (num) {
      case 1:
        return 'UN';
      case 2:
        return 'DOS';
      case 3:
        return 'TRES';
      case 4:
        return 'CUATRO';
      case 5:
        return 'CINCO';
      case 6:
        return 'SEIS';
      case 7:
        return 'SIETE';
      case 8:
        return 'OCHO';
      case 9:
        return 'NUEVE';
      default:
        return '';
    }
  }

  function decenasY(strSin, numUnidades) {
    if (numUnidades > 0) {
      return strSin + ' Y ' + unidades(numUnidades);
    }

    return strSin;
  }

  function decenas(num) {
    var numDecena = Math.floor(num / 10);
    var numUnidad = num - numDecena * 10;

    switch (numDecena) {
      case 1:
        switch (numUnidad) {
          case 0:
            return 'DIEZ';
          case 1:
            return 'ONCE';
          case 2:
            return 'DOCE';
          case 3:
            return 'TRECE';
          case 4:
            return 'CATORCE';
          case 5:
            return 'QUINCE';
          default:
            return 'DIECI' + unidades(numUnidad);
        }
      case 2:
        switch (numUnidad) {
          case 0:
            return 'VEINTE';
          default:
            return 'VEINTI' + unidades(numUnidad);
        }
      case 3:
        return decenasY('TREINTA', numUnidad);
      case 4:
        return decenasY('CUARENTA', numUnidad);
      case 5:
        return decenasY('CINCUENTA', numUnidad);
      case 6:
        return decenasY('SESENTA', numUnidad);
      case 7:
        return decenasY('SETENTA', numUnidad);
      case 8:
        return decenasY('OCHENTA', numUnidad);
      case 9:
        return decenasY('NOVENTA', numUnidad);
      case 0:
        return unidades(numUnidad);
      default:
        return '';
    }
  }

  function centenas(num) {
    var numCentenas = Math.floor(num / 100);
    var numDecenas = num - numCentenas * 100;

    switch (numCentenas) {
      case 1:
        if (numDecenas > 0) {
          return 'CIENTO ' + decenas(numDecenas);
        }
        return 'CIEN';
      case 2:
        return 'DOCIENTOS ' + decenas(numDecenas);
      case 3:
        return 'TRECIENTOS ' + decenas(numDecenas);
      case 4:
        return 'CUATROCIENTOS ' + decenas(numDecenas);
      case 5:
        return 'QUINIENTOS ' + decenas(numDecenas);
      case 6:
        return 'SEISCIENTOS ' + decenas(numDecenas);
      case 7:
        return 'SETECIENTOS ' + decenas(numDecenas);
      case 8:
        return 'OCHOCIENTOS ' + decenas(numDecenas);
      case 9:
        return 'NOVECIENTOS ' + decenas(numDecenas);
      default:
        return decenas(numDecenas);
    }
  }

  function seccion(num, divisor, strSingular, strPlural) {
    var numCientos = Math.floor(num / divisor);
    var numResto = num - numCientos * divisor;

    var letras = '';

    if (numCientos > 0) {
      if (numCientos > 1) {
        letras = centenas(numCientos) + ' ' + strPlural;
      } else {
        letras = strSingular;
      }
    }

    if (numResto > 0) {
      letras += '';
    }

    return letras;
  }

  function miles(num) {
    var divisor = 1000;
    var numCientos = Math.floor(num / divisor);
    var numResto = num - numCientos * divisor;
    var strMiles = seccion(num, divisor, 'UN MIL', 'MIL');
    var strCentenas = centenas(numResto);

    if (strMiles === '') {
      return strCentenas;
    }

    return (strMiles + ' ' + strCentenas).trim();
  }

  function millones(num) {
    var divisor = 1000000;
    var numCientos = Math.floor(num / divisor);
    var numResto = num - numCientos * divisor;
    var strMillones = seccion(num, divisor, 'UN MILLÓN DE', 'MILLONES DE');
    var strMiles = miles(numResto);

    if (strMillones === '') {
      return strMiles;
    }

    return (strMillones + ' ' + strMiles).trim();
  }

  function NumerosALetras(num) {
    const data = {
      numero: num,
      enteros: Math.floor(num),
      centavos: Math.round(num * 100) - Math.floor(num) * 100,
      letrasCentavos: '',
      letrasMonedaPlural: 'SOLES',
      letrasMonedaSingular: 'SOL',
      letrasMonedaCentavoPlural: '/100 CMS.',
      letrasMonedaCentavoSingular: '/100 CMS.'
    };

    if (data.centavos >= 0) {
      data.letrasCentavos = function () {
        if (data.centavos >= 1 && data.centavos <= 9) {
          return '0' + data.centavos + data.letrasMonedaCentavoSingular;
        }

        if (data.centavos === 0) {
          return '00' + data.letrasMonedaCentavoSingular;
        }

        return data.centavos + data.letrasMonedaCentavoPlural;
      }();
    }

    if (data.enteros === 0) {
      return ('CERO ' + data.letrasMonedaPlural + ' ' + data.letrasCentavos).trim();
    }

    if (data.enteros === 1) {
      return (millones(data.enteros) + ' ' + data.letrasMonedaSingular + ' ' + data.letrasCentavos).trim();
    }

    return (millones(data.enteros) + ' ' + data.letrasMonedaPlural + ' ' + data.letrasCentavos).trim();
  }
