import { PROJEKTPage } from './app.po';

describe('projekt App', function() {
  let page: PROJEKTPage;

  beforeEach(() => {
    page = new PROJEKTPage();
  });

  it('should display message saying app works', () => {
    page.navigateTo();
    expect(page.getParagraphText()).toEqual('app works!');
  });
});
