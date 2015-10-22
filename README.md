# Silex Modular Skeleton

Esse Skeleton destina-se a quem deseja trabalhar com Silex de formulá **Modular** e seguindo o design **MVC**.

O Silex foi organizado em uma estrutura de pastas semelhante a Frameworks completas, porém não há nada além do puro Silex.

Seguindo o conceito de **Modularização**, nada que foi adicionado por mim é obrigatório.

## Composer

O projeto depende do composer para ser instalado. Segue links de referência para utilização do composer:

## Links
* [Documentação composer](https://getcomposer.org/doc)
* [Silex](http://silex.sensiolabs.org/)

## Descrição de diretórios

Como citado acima, o projeto está dividido em uma estrutura bem similar a grandes frameworks. Nesse caso, como trabalho com ZF2, me inspirei muito nele para esse Skeleton.

### config

Esse diretório contém os arquivos de configuração de URLs, DB e E-mail. Caso não queira usar esses arquivos, tudo bem, não é necessário. Porém há um sistema de **autoload** que carrega esses arquivos automaticamente (e isso é uma mão na roda). Antes de altera-lo com seus dados, leia a nota colocada como comentário no ínicio do arquivo.

### modules
Esse diretório contém os módulos do sistema. Por padrão acompanha dois módulos, o Base e o Exemplo. O módulo Base contém uma serie de Helpers que podem agilizar o desenvolvimento de sua aplicação. Já o Exemplo é um módulo que você pode usar como referência para criar o seu.

Para criar seus módulos você deve ir até o arquivo *src/bootstrap.php* e, na linha 35, adicionar o nome do seu módulo seguindo o padrão já existente. Caso queira desativar um dos módulos padrões que seguem esse exemplo, remova-os da lista.

Lembrando que nenhum dos dois módulos são obrigatórios.

### public
Aqui temos o arquivo *index.php* e um *.htaccess* simples que força o acesso sempre ser feito para a **index**. Nesse arquivo apenas definimos uma constante com o diretório raiz do projeto e chamamos o bootstrap (falarei sobre ele abaixo).

### src
Esse diretório talvez seja o mais complexo, mas não necessita de alteração para instalação do projeto. Segue breve descrição dos arquivos:

* **autoload.php**  - Esse arquivo contém duas funçõe que servem para carregar todos os arquivos **.php** dos módulos.
* **bootstrap.php** - Executa as ações de auto-carregamento e inicia a aplicação do Silex (**Silex\Application**).
* **doctypes.php**  - Apenas define quais os tipos de doctype para de documento. Isso serve para auxiliar no desenvolvimento front-end. **Não Obrigatório**
* **mimes.php**     - Apenas define os MIME TYPES mais comuns para auxiliar no desenvolvimento back-end.  **Não Obrigatório**

## Conclusão

É possível observar que não houve alterações no método de trabalhar com o Silex, houve apenas uma reorganização ;)

#### Dúvidas, sugestões e reclamações:

kayo.almeida.dev@gmail.com