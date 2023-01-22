<p align="center">
  <a href="">
    <img src="https://www.php.net/images/logos/php-logo.svg" width="30%">
  </a>
  <h3 align="center">Sistema Finanças</h3>
  <h4 align="center">Sistemas para controle de finanças pessoais</h4>
  <h4 align="center"> 
	🚧  7.4.*  🚧 <br>
	🚧  Porjeto Descontinuado  🚧
</h4>

Não segue padrão de código e nem todas partes do projeto há uso de boas práticas. Esta criação é para uso pessoal e para realização de novas funcionalidade e estudos diários.

</p>

## Instalação
Necessário descompactar a pasta no servidor e alterar as constantes gerais do sistema no arquivo App.php.
```
define('EMAIL_DESENVOLVEDOR', 'email@seudominio.com.br');
define('RAIZ_SITE', $_SERVER['DOCUMENT_ROOT'] . "/financas"); 
define('NOME_SITE', $_SERVER['SERVER_NAME']);
define('APP_HOST', $_SERVER['HTTP_HOST'] . "/financas");
define('PATH', realpath('./'));
define('TITLE', "Financas " . (Lib\Sessao::retornaUsuario() ) ) ;
define('DB_HOST', "localhost");
 ```
 Atenção na URI pois o desenvolvimento foi feito em uma subpasta do host principal.
 
 ## Sobre o Finanças
Feito sobre estrutura MVC POO com funcionalidades básicas como:
 - Cadastros
    - Usuário
    - Crédito / Débito
    - Contas a pagar / Contas a receber
    - Estabelecimentos
    - Forma de pagamento        
    - Relatos de erros
 - Relatórios
   - Todos os itens do item de cadastro
   - Extrato por período
   - Custo total de cada produto
   - Movimentações sem comprovantes 
- Processos automaticos (RPA)
   - Valores vencidos de contas pagar/receber para creditar/debitar
   - Calcula saldo futuro
   - Envio de email por operação
   - Gráfico de rentabilidade
- Operações
    - Alteração para base de teste e produção
    - Exportação da conta em aquivos (JSON, XML e CSV(em construção) )
    - Bloqueio de competência
  
## Tecnologias Usadas
- [PHP7](https://www.php.net/)
- [HTML 5](https://developer.mozilla.org/pt-BR/docs/Web/HTML)
- [CSS 3])(https://www.w3schools.com/css/)
- [JavaScript](https://developer.mozilla.org/pt-BR/docs/Web/JavaScript)
- [Mysql](https://www.mysql.com/)

### Autor
- [Rodolfo J.Silva](https://github.com/lrodolfol) (Developer)
- [LinkeIn](https://www.linkedin.com/in/rodolfoj-silva/)
- Email: (rodolfo0ti@gmail.com)

## License
The MIT License (MIT).
