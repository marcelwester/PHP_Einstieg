import { StatusPage } from './app.po';

describe('status App', function() {
  let page: StatusPage;

  beforeEach(() => {
    page = new StatusPage();
  });

  it('should display message saying app works', () => {
    page.navigateTo();
    expect(page.getParagraphText()).toEqual('app works!');
  });
});
