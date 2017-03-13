<h1 align="center">OverSearch by Composer</h1>
<p>해당 어플리케이션은 의존성 관리도구 Composer를 학습하기 위한 용도로 구현하였습니다.</p>
<p>Psr4, autoload, 의존성 관리도구를 통한 Log, phpdocumentor 등 라이브러리 적용하였습니다.</p>
<p>UI 구성은 bootstrap 프레임워크로 구현하였습니다.</p>
| 항목        | 내용           |
| ------------- |:-------------:|
| 프로젝트명      | OverSearch |
| 프로젝트 설명      | php7의 퍼포먼스와 컴포저의 의존성을 활용하기 위해 구현 |
| 개발기간      | 2016.9.12 ~ 9.29 |
| 개발인원      | 단독 |
| 작업환경      | macOs-vagrant |
| 서버환경      | AWS - ubuntu 16.04 |
| framework      | Only Composer |
| site      | [OverSearch](http://javamon.be/ "OverSearch" target="_blank") |
<hr>
<h4>초기 화면</h4>
<p>같은 기능의 타 사이트와 같이, 심플한 검색전 뷰를 구성하였습니다.</p>
<div align="center">
    <img src="https://github.com/javamon1174/OverSearch/blob/master/%20screenshot/init.png?raw=true" />
</div>

<h4>검색 화면 - Main</h4>
<p>검색 뷰로 들어오면 블리자드 웹의 데이터를 파싱하여 데이터베이스에 저장 후, 뷰에 바인딩하여 보여줍니다.</p>
<p>기존 데이터가 있을 시 데이터베이스에서 데이터를 가져와 뷰에 바인딩하여 보여줍니다.</p>
<p>전적갱신 버튼을 통해 기존 데이터를 데이터베이스에서 삭제하고 새로 파싱합니다.</p>
<p>영웅 픽순위의 영웅 이름를 클릭하게 되면 #id값에 따라 디테일 뷰의 위치를 이동하도록 구현하였습니다.</p>
<div align="center">
    <img src="https://github.com/javamon1174/OverSearch/blob/master/%20screenshot/search.png?raw=true" />
</div>

<h4>검색 화면 - Detail</h4>
<p>데이터 수에 따라 동적으로 표현하는 레이아웃을 적용하였습니다.</p>
<div align="center">
    <img src="https://github.com/javamon1174/OverSearch/blob/master/%20screenshot/detail.png?raw=true" />
</div>
